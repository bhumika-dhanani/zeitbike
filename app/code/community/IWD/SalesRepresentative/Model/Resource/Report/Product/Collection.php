<?php

class IWD_SalesRepresentative_Model_Resource_Report_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{


    protected $_periodFormat;
    protected $_selectedColumns = array();
    protected $_from = null;
    protected $_to = null;
    protected $_orderStatus = null;
    protected $_user = null;
    protected $_storesIds = 0;
    protected $_applyFilters = true;
    protected $_isTotals = false;
    protected $_isSubTotals = true;
    protected $_aggregatedColumns = array();

    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */
    public function __construct()
    {
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/order_item');
        $this->setConnection($this->getResource()->getReadConnection());
    }


    /**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();


        if ($this->isTotals()) {
            $this->_selectedColumns = array(
                'period' => $this->_periodFormat,
                'created_at' => 'order.created_at',
                'base_row_invoiced' => 'SUM(IF (`main_table`.`base_row_invoiced` > 0, `main_table`.`base_row_invoiced`, `conf`.`base_row_invoiced`))',
                'qty_invoiced' => 'SUM(main_table.qty_invoiced)',
                'amount_refunded' => 'SUM(IF (`main_table`.`amount_refunded` > 0, `main_table`.`amount_refunded`, `conf`.`amount_refunded`))',
                'earned' => $this->_selectEarned(true),
                'name' => 'name'
            );
        }

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'period' => $this->_periodFormat,
                'created_at' => 'order.created_at',
                'base_row_invoiced' => 'IF (`main_table`.`base_row_invoiced` > 0, `main_table`.`base_row_invoiced`, `conf`.`base_row_invoiced`)	',
                'qty_invoiced' => 'main_table.qty_invoiced',
                'amount_refunded' => 'IF (`main_table`.`amount_refunded` > 0, `main_table`.`amount_refunded`, `conf`.`amount_refunded`)',
                'base_price' => 'IF (`main_table`.`base_price` > 0, `main_table`.`base_price`, `conf`.`base_price`)',
                'earned' => $this->_selectEarned(),
                'name' => 'name'

            );
        }

        return $this->_selectedColumns;
    }

    protected function _selectEarned($summ = false)
    {
        $field = "
						CASE
							WHEN `main_table`.`base_row_invoiced` > 0 THEN
								CASE 
									WHEN iwd_product_rate_type IN('1','2') AND iwd_global!=1 THEN
										CASE  
											WHEN iwd_product_rate_type = '1' THEN iwd_product_percent_rate * (main_table.base_row_invoiced-main_table.amount_refunded - main_table.base_discount_amount) / 100
											WHEN iwd_product_rate_type = '2' THEN iwd_fixed_rate * main_table.qty_invoiced
										END
									WHEN iwd_product_rate_type NOT IN('1','2') OR iwd_global=1 THEN
										CASE  
											WHEN iwd_rate_type = '1' THEN iwd_percent_rate * (main_table.base_row_invoiced-main_table.amount_refunded - main_table.base_discount_amount) / 100
											WHEN iwd_rate_type = '2' THEN iwd_fixed_rate  * main_table.qty_invoiced
										END
								END		
					
							WHEN `conf`.`base_row_invoiced` > 0	THEN
								CASE 
									WHEN iwd_product_rate_type IN('1','2') AND iwd_global!=1 THEN
										CASE  
											WHEN iwd_product_rate_type = '1' THEN iwd_product_percent_rate * (conf.base_row_invoiced-conf.amount_refunded - conf.base_discount_amount) / 100
											WHEN iwd_product_rate_type = '2' THEN iwd_fixed_rate * main_table.qty_invoiced
										END
									WHEN iwd_product_rate_type NOT IN('1','2') OR iwd_global=1 THEN
										CASE  
											WHEN iwd_rate_type = '1' THEN iwd_percent_rate * (conf.base_row_invoiced-conf.amount_refunded - conf.base_discount_amount) / 100
											WHEN iwd_rate_type = '2' THEN iwd_fixed_rate  * main_table.qty_invoiced
										END
								END	
				
						END				
		";

        if (!$summ) {
            return $field;
        } else {
            return 'SUM(' . $field . ')';
        }
    }

    protected function _initSelect()
    {

        $colunms = $this->_getSelectedColumns();

        $this->getSelect()->from(array('main_table' => $this->getTable('sales/order_item')), $colunms)
            ->joinLeft(array('conf' => $this->getTable('sales/order_item')), 'main_table.parent_item_id=conf.item_id', array())
            ->joinLeft(array('order' => $this->getTable('sales/order')), 'main_table.order_id=order.entity_id', array())
            ->joinLeft(array('link' => $this->getTable('salesrep/link')), 'main_table.product_id=link.iwd_linked_product_id', "link.*")
            ->joinLeft(array('link_user' => $this->getTable('salesrep/users')), 'link.iwd_user_id=link_user.iwd_entity_id', array('iwd_global', 'iwd_rate_type', 'iwd_percent_rate', 'iwd_fixed_rate'))
            ->joinLeft(array('user_table' => $this->getTable('admin/user')), 'link_user.iwd_user_id=user_table.user_id', "CONCAT(user_table.firstname,' ', user_table.lastname) as username")
            ->where('((main_table.qty_invoiced != 0 AND main_table.base_row_invoiced != 0) || (conf.qty_invoiced != 0 AND conf.base_row_invoiced !=0))');
        $this->_applyDateRangeFilter();
        $this->_applyOrderStatusFilter();
        $this->_applyStoreFilter();
        $this->_applyUserFilter();

        $this->getSelect()->order('order.created_at ASC');

        return $this;
    }

    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        return $this;
    }

    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    public function setDateRange($from = null, $to = null)
    {
        $zone = Mage::getStoreConfig('general/locale/timezone');
        $offset = Mage::getModel('core/date')->getGmtOffset($zone);
        $add = false;
        if ($offset < 0) {
            $add = true;
        }
        $offset = $offset * (-1);
        $offset = $offset - 3600;


        if (!empty($from)) {

            $date = new Zend_Date(strtotime($from));

            $date->setTimezone("UTC");
            if ($add) {
                $date->addSecond($offset);
            } else {
                $date->subSecond($offset);
            }
            $this->_from = $date->toString('YYYY-MM-dd HH:mm:ss');
        }

        if (!empty($to)) {
            $date = new Zend_Date(strtotime($to));

            $date->setHour(23);
            $date->setMinute(59);
            $date->setSecond(59);

            $date->setTimezone('UTC');
            if ($add) {
                $date->addSecond($offset);
            } else {
                $date->subSecond($offset);
            }

            $this->_to = $date->toString('YYYY-MM-dd HH:mm:ss');
        }
        return $this;
    }

    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->getSelect()->where('order.created_at >= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where('order.created_at <= ?', $this->_to);
        }
        return $this;
    }

    protected function _applyOrderStatusFilter()
    {
        if (is_array($this->_orderStatus)) {
            $this->getSelect()->where('order.status IN (?)', $this->_orderStatus);
        }
    }

    protected function _applyUserFilter()
    {
        $requestData = Mage::helper('adminhtml')->prepareFilterString(Mage::app()->getRequest()->getParam('filter'));
        if (isset ($requestData ['users']) && !empty ($requestData ['users'])) {
            $this->getSelect()->where('user_table.user_id = ?', $requestData ['users']);
        } else {
            //try get user from session (notification logic)
            $user = Mage::getSingleton('admin/session')->getUserRepresentative();
            $this->getSelect()->where('user_table.user_id = ?', $user);
        }
    }

    protected function _applyStoreFilter()
    {
        if (is_array($this->_storesIds) & count($this->_storesIds) > 0) {
            $this->getSelect()->where('main_table.store_id IN (?)', $this->_storesIds);
        }
    }

    public function setApplyFilters($flag)
    {
        $this->_applyFilters = $flag;
        return $this;
    }

    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        if ($this->_applyFilters) {
            $this->_applyDateRangeFilter();
        }
        return parent::load($printQuery, $logQuery);
    }


}