<?php

namespace Kitchen365\Twilio\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Kitchen365\Twilio\Model\Log::class,
            \Kitchen365\Twilio\Model\ResourceModel\Log::class
        );
    }
}
