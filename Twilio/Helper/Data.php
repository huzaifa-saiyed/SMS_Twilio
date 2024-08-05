<?php

namespace Kitchen365\Twilio\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const GENERAL_CONFIG_PATH = 'sales_sms/general/';
    const ORDER_CONFIG_PATH = 'sales_sms/order/';
    const INVOICE_CONFIG_PATH = 'sales_sms/invoice/';
    const SHIPMENT_CONFIG_PATH = 'sales_sms/shipment/';
    const CONTACT_US_CONFIG_PATH = 'sales_sms/customer_sms/';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isTwilioEnabled()
    {
        if ($this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'enabled',
            ScopeInterface::SCOPE_STORE
        )) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getAccountSid()
    {
        return $this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'account_sid',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getAccountAuthToken()
    {
        $test = $this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'auth_token',
            ScopeInterface::SCOPE_STORE
        );
        return $this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'auth_token',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getTwilioPhone()
    {
        return $this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'twilio_phone',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->scopeConfig->getValue(
            self::GENERAL_CONFIG_PATH . 'log_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isOrderMessageEnabled()
    {
        if ($this->scopeConfig->getValue(self::ORDER_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
            && $this->isTwilioEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRawOrderMessage()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_CONFIG_PATH . 'message',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isInvoiceMessageEnabled()
    {
        if ($this->scopeConfig->getValue(self::INVOICE_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
            && $this->isTwilioEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getRawInvoiceMessage()
    {
        return $this->scopeConfig->getValue(
            self::INVOICE_CONFIG_PATH . 'message',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isShipmentMessageEnabled()
    {
        if ($this->scopeConfig->getValue(self::SHIPMENT_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
            && $this->isTwilioEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getRawShipmentMessage()
    {
        return $this->scopeConfig->getValue(
            self::SHIPMENT_CONFIG_PATH . 'message',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isContactUsMessageEnabled()
    {
        if ($this->scopeConfig->getValue(self::CONTACT_US_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
            && $this->isTwilioEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getContactUsMessage()
    {
        return $this->scopeConfig->getValue(
            self::CONTACT_US_CONFIG_PATH . 'message',
            ScopeInterface::SCOPE_STORE
        );
    }
}
