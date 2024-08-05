<?php

namespace Kitchen365\Twilio\Model\Adapter\Order;

use Magento\Store\Model\StoreManagerInterface;
use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Helper\MessageTemplateParser;
use Kitchen365\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order\Invoice as SalesInvoice;
use Magento\Shipping\Model\CarrierFactory;
use Psr\Log\LoggerInterface;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Invoice extends AdapterAbstract
{
    /**
     * @var int
     */
    protected $entityTypeId = 2;

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
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Kitchen365\Twilio\Model\Adapter\Order\Invoice
     */
    public function sendOrderSms(SalesInvoice $invoice)
    {
        if (!$this->_helper->isInvoiceMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawInvoiceMessage(),
            $this->getInvoiceVariables($invoice)
        );

        $order = $invoice->getOrder();

        if (!$order) {
            $orderId = $invoice->getOrderId();
            $order = $this->orderRepository->getById($orderId);
        }

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = $order->getBillingAddress()->getTelephone();

        $this->entityId = $invoice->getId();
        $this->email = $order->getCustomerEmail();
        $this->name = $order->getCustomerFirstname();
        $this->_sendSms();

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return array
     */
    protected function getInvoiceVariables($invoice)
    {
        $vars = [];

        $vars['invoice.qty'] = $invoice->getTotalQty();
        $vars['invoice.grandtotal'] = $invoice->getGrandTotal(); //TODO: not properly formatted
        $vars['invoice.increment_id'] = $invoice->getIncrementId();
        $vars['order.increment_id'] = $invoice->getOrder()->getIncrementId();
        $vars['order.qty'] = $invoice->getOrder()->getTotalQtyOrdered();
        $vars['billing.firstname'] = $invoice->getOrder()->getBillingAddress()->getFirstname();
        $vars['billing.lastname'] = $invoice->getOrder()->getBillingAddress()->getLastname();
        $vars['storename'] = $this->_storeManager->getWebsite(
            $this->_storeManager->getStore($invoice->getOrder()->getStoreId())->getWebsiteId()
        )->getName();

        return $vars;
    }
}
