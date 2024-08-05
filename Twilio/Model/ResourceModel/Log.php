<?php

namespace Kitchen365\Twilio\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kitchen365_twilio_log', 'id');
    }
}
