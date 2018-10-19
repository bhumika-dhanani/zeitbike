<?php
class IWD_BirthdayCoupon_Model_Coupons extends Mage_Core_Model_Abstract
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON;
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE;
    protected $XML_PATH_IS_ENABLED;
    protected $XML_PATH_CUSTOMER_GROUPS = 'iwd_coupon/genaral/customer_groups';
    protected $XML_PATH_RULE_ID;
    protected $XML_PATH_EXPIRE_DAYS;
    protected $XML_PATH_EMAIL_IDENTITY;

    protected $notify;
    protected $cause;
    protected $groups_rules;

    public function __construct($cause)
    {
        parent::__construct();

        $this->notify = Mage::getModel('iwd_coupon/notification');
        $this->groups_rules = $this->getCustomerGroupRule();
        $this->cause = $cause;
    }

    public function getCustomerGroupRuleSettingsTable()
    {
        $result = '<table cellpadding="3"><tr><td><b>Group</b></td><td><b>Rule</b></td></tr>';

        foreach ($this->groups_rules as $group => $rule) {
            $result .= '<tr><td>' . Mage::getModel('customer/group')->load($group)->getCustomerGroupCode() . '</td><td>';
            $result .= ($rule=="none") ? "-- Without coupon --" : Mage::getModel('salesrule/rule')->load($rule)->getName();
            $result .= '</td><tr>';
        }

        $result .= "</table>";
        return $result;
    }

    protected function hasGuestCategory()
    {
        $groups = $this->getCustomerGroups();
        return in_array(0, $groups);
    }

    protected function getTemplateId($customer_coupon)
    {
        return $customer_coupon->getCouponCode() ? $this->getEmailTemplateWithCoupon() : $this->getEmailTemplateCouponNone();
    }

    public function isEnabled()
    {
        return Mage::getStoreConfig($this->XML_PATH_IS_ENABLED, Mage::app()->getStore());
    }

    protected function getRulesIds()
    {
        return explode(',',  Mage::getStoreConfig($this->XML_PATH_RULE_ID, Mage::app()->getStore()));
    }

    protected function getEmailTemplateWithCoupon()
    {
        return Mage::getStoreConfig($this->XML_PATH_EMAIL_TEMPLATE_WITH_COUPON, Mage::app()->getStore());
    }

    protected function getEmailTemplateCouponNone()
    {
        return Mage::getStoreConfig($this->XML_PATH_EMAIL_TEMPLATE_COUPON_NONE, Mage::app()->getStore());
    }

    protected function getExpirationDate()
    {
        $days = Mage::getStoreConfig($this->XML_PATH_EXPIRE_DAYS, Mage::app()->getStore());
        if ((int)$days > 0) {
            return date("Y-m-d H:i:s", strtotime("+" . $days . " day"));
        }
        return null;
    }

    protected function getCustomerGroups()
    {
        return explode(',', Mage::getStoreConfig($this->XML_PATH_CUSTOMER_GROUPS, Mage::app()->getStore()));
    }

    protected function sendEmail($item)
    {
        $groupId = $item->getCustomerGroupId();
        if (!isset($this->groups_rules[$groupId]))
            return false;

        $this->addCouponToCustomer($item);
        $item->setCause($this->cause);
        $templateId = $this->getTemplateId($item);
        $sender = $this->getSenderArray();
        if ($this->notify->sendTransactionalEmail($item, $templateId, $sender)) {
            $item->SaveItem();
        }

        return true;
    }

    protected function getSenderArray()
    {
        $identity = Mage::getStoreConfig($this->XML_PATH_EMAIL_IDENTITY, Mage::app()->getStore());

        switch($identity){
            case 'general':
                $name = Mage::getStoreConfig('trans_email/ident_general/name');
                $email = Mage::getStoreConfig('trans_email/ident_general/email');
                break;
            case 'sales':
                $name = Mage::getStoreConfig('trans_email/ident_sales/name');
                $email = Mage::getStoreConfig('trans_email/ident_sales/email');
                break;
            case 'support':
                $name = Mage::getStoreConfig('trans_email/ident_support/name');
                $email = Mage::getStoreConfig('trans_email/ident_support/email');
                break;
            case 'custom1':
                $name = Mage::getStoreConfig('trans_email/ident_custom1/name');
                $email = Mage::getStoreConfig('trans_email/ident_custom1/email');
                break;
            case 'custom2':
                $name = Mage::getStoreConfig('trans_email/ident_custom2/name');
                $email = Mage::getStoreConfig('trans_email/ident_custom2/email');
                break;
            default:
                $name = Mage::getStoreConfig('trans_email/ident_general/name');
                $email = Mage::getStoreConfig('trans_email/ident_general/email');
                break;
        }

        return array(
            'email' => $email,
            'name' => $name
        );
    }

    protected function addCouponToCustomer($item)
    {
        $groupId = $item->getCustomerGroupId();
        $ruleId = $this->groups_rules[$groupId];

        if ($ruleId != 'none') {
            $expirationDate = $this->getExpirationDate();
            $coupon = Mage::getModel('iwd_coupon/coupongenerator')->generateCoupon($ruleId, $expirationDate);
            $rule = Mage::getModel('salesrule/rule')->load($ruleId);
            $item->setCoupon($coupon);
            $item->setRule($rule);
        }
    }

    public function getCustomerGroupRule()
    {
        $rules = $this->getRulesIds();

        $group_rule = array();
        foreach ($rules as $rule_id) {
            $customerGroupIds = ($rule_id == 'none') ? $this->getCustomerGroups() : Mage::getModel('salesrule/rule')->load($rule_id)->getCustomerGroupIds();
            foreach ($customerGroupIds as $group) {
                $group_rule[$group] = $rule_id;
            }
        }

        return $group_rule;
    }

    public function isSentAlready($email, $cause, $days_ago = null)
    {
        $select = Mage::getModel('iwd_coupon/customercoupon')
            ->getCollection()
            ->addFieldToFilter('customer_email', $email)
            ->addFieldToFilter('cause', $cause);
        if ($days_ago !== null && (int)$days_ago > 0){
            $last_date = date('Y-m-d H:i:s', strtotime("-" . $days_ago . " day"));
            $select = $select->addFieldToFilter('date_receive', array("from" => $last_date, "datetime" => true));
        }

        return ($select->count() > 0);
    }
}