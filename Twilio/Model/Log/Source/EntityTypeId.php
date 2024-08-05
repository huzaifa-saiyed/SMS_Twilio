<?php

namespace Kitchen365\Twilio\Model\Log\Source;

use Magento\Framework\Data\OptionSourceInterface;

class EntityTypeId implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Order', 'value' => 1],
            ['label' => 'Invoice', 'value' => 2],
            ['label' => 'Shipment', 'value' => 3],
            ['label' => 'Contact Us', 'value' => 4],
        ];
    }
}
