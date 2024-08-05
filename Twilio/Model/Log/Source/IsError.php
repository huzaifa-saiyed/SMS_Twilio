<?php

namespace Kitchen365\Twilio\Model\Log\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsError implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'No', 'value' => 0],
            ['label' => 'Yes', 'value' => 1]
        ];
    }
}
