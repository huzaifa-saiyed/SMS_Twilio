<?php
 

namespace Kitchen365\Twilio\Plugin\Shipping\Block\Adminhtml;

use Kitchen365\Twilio\Helper\Data;
use Magento\Shipping\Block\Adminhtml\View as ShipmentView;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;

class View
{
    /** @var \Kitchen365\Twilio\Helper\Data */
    protected $_helper;

    /** @var \Magento\Framework\UrlInterface */
    protected $_urlBuilder;

    /** @var \Magento\Framework\AuthorizationInterface */
    protected $_authorization;

    public function __construct(
        Data $helper,
        UrlInterface $url,
        AuthorizationInterface $authorization
    ) {
        $this->_helper = $helper;
        $this->_urlBuilder = $url;
        $this->_authorization = $authorization;
    }

    public function beforeSetLayout(ShipmentView $view)
    {
        if (!$this->_helper->isShipmentMessageEnabled()
            || !$this->_isAllowedSmsAction()
        ) {
            return;
        }

        $message = __('Are you sure you want to send a SMS to the customer?');
        $url = $this->_urlBuilder->getUrl('twilio/shipment/send',
            ['id' => $view->getShipment()->getId()]);

        $view->addButton(
            'send_shipment_sms',
            [
                'label' => __('Send Shipment SMS'),
                'class' => 'send-sms',
                'onclick' => "confirmSetLocation('{$message}', '{$url}')"
            ]
        );
    }

    protected function _isAllowedSmsAction()
    {
        return $this->_authorization->isAllowed('Kitchen365_Twilio::sms');
    }
}
