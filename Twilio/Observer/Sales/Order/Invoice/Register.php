<?php

namespace Kitchen365\Twilio\Observer\Sales\Order\Invoice;

use Magento\Framework\Event\ObserverInterface;
use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;

class Register implements ObserverInterface
{
    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Kitchen365\Twilio\Model\Adapter\Order\Invoice
     */
    protected $_invoiceAdapter;

    public function __construct(
        Helper $helper,
        InvoiceAdapter $invoiceAdapter
    ) {
        $this->_helper = $helper;
        $this->_invoiceAdapter = $invoiceAdapter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @var \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isTwilioEnabled()) {
            return $observer;
        }

        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();

        $billingAddress = $order->getBillingAddress();

        if ($billingAddress->getSmsAlert()) {
            $this->_invoiceAdapter->sendOrderSms($invoice);
        }

        return $observer;
    }
}
