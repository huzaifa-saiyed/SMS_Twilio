<?php

namespace Kitchen365\Twilio\Plugin\Checkout\Block\Checkout;

use Kitchen365\Twilio\Helper\Data as Helper;

class LayoutProcessor
{
    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * LayoutProcessor constructor.
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->_helper->isTwilioEnabled()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['sms_alert'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox',
                'custom_entry' => null,
            ],
            'dataScope' => 'shippingAddress.custom_attributes.sms_alert',
            'label' => __('SMS Order Notifications'),
            'description' => __('Send SMS order notifications to the phone number above.'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'checked' => true,
            'validation' => [],
            'sortOrder' => 125,
            'custom_entry' => null,
        ];

        return $jsLayout;
    }
}
