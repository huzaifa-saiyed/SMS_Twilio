<?php

namespace Kitchen365\Twilio\Model\Adapter\Order;

use Magento\Store\Model\StoreManagerInterface;
use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Helper\MessageTemplateParser;
use Kitchen365\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order\Shipment as SalesShipment;
use Magento\Shipping\Model\CarrierFactory;
use Psr\Log\LoggerInterface;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Shipment extends AdapterAbstract
{
    /**
     * @var int
     */
    protected $entityTypeId = 3;

    /** @var CarrierFactory */
    protected $carrierFactory;

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    public function __construct(
        Helper $helper,
        TwilioClientFactory $twilioClientFactory,
        LoggerInterface $logger,
        MessageTemplateParser $messageTemplateParser,
        StoreManagerInterface $storeManager,
        \Kitchen365\Twilio\Model\LogRepository $logRepository,
        \Kitchen365\Twilio\Model\LogFactory $logFactory,
        CarrierFactory $carrierFactory,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $helper,
            $twilioClientFactory,
            $logger,
            $messageTemplateParser,
            $storeManager,
            $logRepository,
            $logFactory,
            $urlBuilder
        );
        $this->carrierFactory = $carrierFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Kitchen365\Twilio\Model\Adapter\Order\Shipment
     */
    public function sendOrderSms(SalesShipment $shipment)
    {
        if (!$this->_helper->isShipmentMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawShipmentMessage(),
            $this->getShipmentVariables($shipment)
        );

        $order = $shipment->getOrder();

        if (!$order) {
            $orderId = $shipment->getOrderId();
            $order = $this->orderRepository->getById($orderId);
        }

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = $order->getShippingAddress()->getTelephone();

        $this->entityId = $shipment->getId();
        $this->email = $order->getCustomerEmail();
        $this->name = $order->getCustomerFirstname();
        $this->_sendSms();

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    protected function getShipmentVariables($shipment)
    {
        $vars = [];

        $vars['shipment.qty'] = $shipment->getTotalQty();
        $vars['shipment.trackingnumber'] = $this->getTrackingNumbersArray($shipment->getTracks());
        $vars['shipment.trackinglink'] = $this->getTrackingLinks($shipment->getTracks());
        $vars['shipment.increment_id'] = $shipment->getIncrementId();
        $vars['order.increment_id'] = $shipment->getOrder()->getIncrementId();
        $vars['order.qty'] = $shipment->getOrder()->getTotalQtyOrdered();
        $vars['shipment.firstname'] = $shipment->getShippingAddress()->getLastname();
        $vars['shipment.lastname'] = $shipment->getShippingAddress()->getLastname();
        $vars['storename'] = $this->_storeManager->getWebsite(
            $this->_storeManager->getStore($shipment->getOrder()->getStoreId())->getWebsiteId()
        )->getName();

        return $vars;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Track $items
     * @return array
     */
    protected function getTrackingNumbersArray($items)
    {
        $trackingNumbers = [];
        foreach ($items as $item) {
            $trackingNumbers[] = $item->getNumber();
        }

        return $trackingNumbers;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Track[] $tracks
     * @return array
     */
    protected function getTrackingLinks($tracks)
    {
        $links = [];
        foreach ($tracks as $track) {
            if ($url = $this->getTrackUrl($track)) {
                $links[] = $url;
            }
        }

        return $links;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Track $track
     * @return string|false
     */
    private function getTrackUrl($track)
    {
        $carrierInstance = $this->carrierFactory->create($track->getCarrierCode());
        if (!$carrierInstance) {
            return false;
        }
        $carrierInstance->setStore($track->getStore());

        $trackingInfo = $carrierInstance->getTrackingInfo($track->getNumber());
        if (!$trackingInfo || !$trackingInfo->getUrl()) {
            return false;
        }

        return $trackingInfo->getUrl();
    }
}
