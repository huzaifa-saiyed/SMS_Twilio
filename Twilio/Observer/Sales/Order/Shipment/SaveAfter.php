<?php

namespace Kitchen365\Twilio\Observer\Sales\Order\Shipment;

use Magento\Framework\Event\ObserverInterface;
use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Model\Adapter\Order\Shipment as ShipmentAdapter;

class SaveAfter implements ObserverInterface
{
    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Kitchen365\Twilio\Model\Adapter\Order\Shipment
     */
    protected $_shipmentAdapter;

    public function __construct(
        Helper $helper,
        ShipmentAdapter $shipmentAdapter
    ) {
        $this->_helper = $helper;
        $this->_shipmentAdapter = $shipmentAdapter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @var \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isTwilioEnabled()) {
            return $observer;
        }

        $shipment = $observer->getShipment();
        $order = $shipment->getOrder();

        if (!$shippingAddress = $order->getShippingAddress()) {
            return $observer;
        }

        if ($shippingAddress->getSmsAlert()) {
            $this->_shipmentAdapter->sendOrderSms($shipment);
        }

        return $observer;
    }
}
