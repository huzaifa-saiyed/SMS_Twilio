<?php

namespace Kitchen365\Twilio\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Model\Adapter\Order as OrderAdapter;

class OrderAfter implements ObserverInterface
{
    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Kitchen365\Twilio\Model\Adapter\Order
     */
    protected $_orderAdapter;

    public function __construct(
        Helper $helper,
        OrderAdapter $orderAdapter
    ) {
        $this->_helper = $helper;
        $this->_orderAdapter = $orderAdapter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isTwilioEnabled()) {
            return $observer;
        }

        $order = $observer->getOrder();

        $billingAddress = $order->getBillingAddress();

        if ($billingAddress->getSmsAlert()) {
            $this->_orderAdapter->sendOrderSms($order);
        }

        return $observer;
    }
}
