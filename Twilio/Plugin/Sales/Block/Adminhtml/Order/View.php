<?php
 

namespace Kitchen365\Twilio\Plugin\Sales\Block\Adminhtml\Order;

use Kitchen365\Twilio\Helper\Data;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
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

    public function beforeSetLayout(OrderView $view)
    {
        if (!$this->_helper->isOrderMessageEnabled()
            || !$this->_isAllowedSmsAction()
            || $view->getOrder()->isCanceled()
        ) {
            return;
        }

        $message = __('Are you sure you want to send a SMS to the customer?');
        $url = $this->_urlBuilder->getUrl(
            'twilio/order/send',
            ['id' => $view->getOrderId()]
        );

        $view->addButton(
            'send_order_sms',
            [
                'label' => __('Send Order SMS'),
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
