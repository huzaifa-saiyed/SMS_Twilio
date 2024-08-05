<?php
 

namespace Kitchen365\Twilio\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();

        if ($shippingAddress->getExtensionAttributes()) {
            $shippingAddress->setSmsAlert((int)$shippingAddress->getExtensionAttributes()->getSmsAlert());
        } else {
            $shippingAddress->setSmsAlert(0);
        }

        if ($billingAddress->getExtensionAttributes()) {
            $billingAddress->setSmsAlert((int)$billingAddress->getExtensionAttributes()->getSmsAlert());
        } else {
            $billingAddress->setSmsAlert(0);
        }
    }
}
