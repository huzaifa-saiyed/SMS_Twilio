<?php

namespace Kitchen365\Twilio\Model\Adapter;

use Kitchen365\Twilio\Model\Adapter\AdapterAbstract;
use Magento\Sales\Model\Order as SalesOrder;

class Order extends AdapterAbstract
{
    /**
     * @var int
     */
    protected $entityTypeId = 1;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Kitchen365\Twilio\Model\Adapter\Order
     */
    public function sendOrderSms(SalesOrder $order)
    {
        if (!$this->_helper->isOrderMessageEnabled()) {
            return $this;
        }

        $this->_message = $this->_messageTemplateParser->parseTemplate(
            $this->_helper->getRawOrderMessage(),
            $this->getOrderVariables($order)
        );

        //TODO: something needs to verify the phone number
        //      and add country code
        $this->_recipientPhone = $order->getBillingAddress()->getTelephone();

        $this->entityId = $order->getId();
        $this->email = $order->getCustomerEmail();
        $this->name = $order->getCustomerFirstname();
        $this->_sendSms();

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function getOrderVariables($order)
    {
        $vars = [];

        $vars['order.increment_id'] = $order->getIncrementId();
        $vars['order.qty'] = $order->getTotalQtyOrdered();
        $vars['billing.firstname'] = $order->getBillingAddress()->getFirstname();
        $vars['billing.lastname'] = $order->getBillingAddress()->getLastname();
        $vars['order.grandtotal'] = $order->getGrandTotal(); //TODO: not properly formatted
        $vars['storename'] = $this->_storeManager->getWebsite(
            $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
        )->getName();

        return $vars;
    }
}
