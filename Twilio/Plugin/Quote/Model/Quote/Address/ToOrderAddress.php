<?php
 

namespace Kitchen365\Twilio\Plugin\Quote\Model\Quote\Address;

class ToOrderAddress
{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address $address,
        $data = []
    ) {
        $result = $proceed($address, $data);

        if ($address->getSmsAlert()) {
            $result->setSmsAlert($address->getSmsAlert());
        }

        return $result;
    }
}
