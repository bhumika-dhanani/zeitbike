<?php

class IWD_SalesRepresentative_Model_Resource_Report_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{


    protected $_periodFormat;
    protected $_selectedColumns = array();
    protected $_selectedColumnsForCost = array();
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
        $this->_resource = Mage::getResourceModel('salesrep/sales');
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

        $this->_selectedColumnsForCost = array(
            'period' => $this->_periodFormat,
            'created_at' => 'order_table' . '.' . 'created_at',
            'base_subtotal' => 'order_table' . '.' . 'base_subtotal',
            'base_total_invoiced' => 'order_table' . '.' . 'base_total_invoiced',
            'base_total_refunded' => 'order_table' . '.' . 'base_total_refunded',
            'base_tax_amount' => 'order_table' . '.' . 'base_tax_amount',
            'base_shipping_amount' => 'order_table' . '.' . 'base_shipping_amount',
            'base_discount_amount' => 'order_table' . '.' . 'base_discount_amount',
            'base_subtotal_canceled' => 'order_table' . '.' . 'base_subtotal_canceled',
            'increment_id' => 'order_table' . '.' . 'increment_id',
            'earned' => $this->_selectEarned(),

        );
//		if ($this->isTotals()) {
//			$this->_selectedColumns = array(
//					'period'                         	=> $this->_periodFormat,
//					'created_at'            		 	=> 'order_table'.'.'.'created_at',
//					'base_subtotal'             		=> 'SUM('.'order_table'.'.'.'base_subtotal)',
//					'base_total_invoiced'             	=> 'SUM('.'order_table'.'.'.'base_total_invoiced)',
//					'base_total_refunded'             	=> 'SUM('.'order_table'.'.'.'base_total_refunded)',
//					'base_tax_amount'             		=> 'SUM('.'order_table'.'.'.'base_tax_amount)',
//					'base_discount_amount'             	=> 'SUM('.'order_table'.'.'.'base_discount_amount)',
//					'base_subtotal_canceled'            => 'SUM('.'order_table'.'.'.'base_subtotal_canceled)',
//					'increment_id'             		 	=> 'order_table'.'.'.'increment_id',
//					'earned'							=> $this->_selectEarned(true),
//			);
//		}

//		if (!$this->isTotals()) {
        $this->_selectedColumns = $this->_selectedColumnsForCost;
//		}

        return $this->_selectedColumns;
    }


    protected function _selectEarned($summ = false)
    {
        $field = "
				CASE
				
					WHEN order_table.base_total_refunded IS NOT NULL THEN
				
						CASE
							WHEN iwd_rate_type_order = '1' THEN iwd_percent_rate_order * (order_table.base_total_invoiced-order_table.base_total_refunded) / 100
							WHEN iwd_rate_type_order = '2' THEN iwd_fixed_rate_order
						END
				
					WHEN order_table.base_total_refunded IS NULL THEN
						CASE
							WHEN iwd_rate_type_order = '1' THEN iwd_percent_rate_order * order_table.base_total_invoiced / 100
							WHEN iwd_rate_type_order = '2' THEN iwd_fixed_rate_order
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
        $this->getSelect()->from(array('main_table' => $this->getTable('salesrep/sales')))
            ->joinLeft(array('order_table' => $this->getTable('sales/order')), 'main_table.iwd_order_id=order_table.entity_id', $this->_selectedColumns)
            ->joinLeft(array('user_table' => $this->getTable('admin/user')), 'main_table.iwd_user_id=user_table.user_id', "CONCAT(`firstname`,' ', `lastname`) as username")
            ->joinLeft(array('link_user' => $this->getTable('salesrep/users')), 'link_user.iwd_user_id=user_table.user_id', array('iwd_rate_type_order', 'iwd_percent_rate_order', 'iwd_fixed_rate_order'));
//                    ->joinLeft ( array('order_item' => $this->getTable('sales/order_item')), 'main_table.order_id = order_item.order_id', "SUM(`order_item`.`base_cost`) as cost");

        $this->_applyDateRangeFilter();
        $this->_applyOrderStatusFilter();
        $this->_applyOrderStatusFilter();
        $this->_applyStoreFilter();
        $this->_applyUserFilter();

        $this->getSelect()->order('created_at ASC');
//		$this->getSelect ()->group( 'order_item.order_id' );

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
            $this->getSelect()->where('order_table.created_at >= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where('order_table.created_at <= ?', $this->_to);
        }
        return $this;
    }

    protected function _applyOrderStatusFilter()
    {
        if (is_array($this->_orderStatus)) {
            $this->getSelect()->where('order_table.status IN (?)', $this->_orderStatus);
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
            $this->getSelect()->where('order_table.store_id IN (?)', $this->_storesIds);
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
        $c = parent::load($printQuery, $logQuery);
        foreach ($c->getItems() as $item) {

            $order = Mage::getModel("sales/order")->load($item->getData('order_id'));
            $ordered_items = $order->getAllItems();
            $sum = 0;
            foreach ($ordered_items as $order_item) {
                $sum += $order_item->getData('base_cost') * $order_item->getData('qty_ordered');
            }
            if ($sum) {
                $item->setData('profit', $item->getData('base_subtotal') - $sum);
            } else {
                $item->setData('profit', 0);
                $sum = 0;
            }

            $item->setData('cost', $sum);

        }
        if ($this->isTotals()) { 
            $f_item = $c->getFirstItem();
            foreach ($c->getItems() as $key => $item) {
                if ($key == 0)
                    continue;
                $f_item->setData('base_subtotal', $f_item->getData('base_subtotal') + $item->getData('base_subtotal'));
                $f_item->setData('base_total_refunded', $f_item->getData('base_total_refunded') + $item->getData('base_total_refunded'));
                $f_item->setData('base_total_invoiced', $f_item->getData('base_total_invoiced') + $item->getData('base_total_invoiced'));
                $f_item->setData('base_tax_amount', $f_item->getData('base_tax_amount') + $item->getData('base_tax_amount'));
                $f_item->setData('base_discount_amount', $f_item->getData('base_discount_amount') + $item->getData('base_discount_amount'));
                $f_item->setData('base_subtotal_canceled', $f_item->getData('base_subtotal_canceled') + $item->getData('base_subtotal_canceled'));
                $f_item->setData('profit', $f_item->getData('profit') + $item->getData('profit'));
                $f_item->setData('cost', $f_item->getData('cost') + $item->getData('cost'));
                $f_item->setData('earned', $f_item->getData('earned') + $item->getData('earned'));
                $c->removeItemByKey($key);
            }
        }

        return $c;
    }


}