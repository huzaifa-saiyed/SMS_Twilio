<?php

namespace Kitchen365\Twilio\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Kitchen365\Twilio\Api\Data\LogInterface;

class Log extends AbstractModel implements IdentityInterface, LogInterface
{
    const CACHE_TAG = 'Kitchen365_twilio_log';

    /** @var string */
    protected $_cacheTag = 'Kitchen365_twilio_log';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Kitchen365\Twilio\Model\ResourceModel\Log::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @return string
     */
    public function getSid()
    {
        return $this->getData('sid');
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->getData('msg');
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->getData('customer_email');
    }
    
    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getData('customer_name');
    }

    /**
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    /**
     * @return string
     */
    public function getRecipientPhone()
    {
        return $this->getData('recipient_phone');
    }

    /**
     * @return boolean
     */
    public function getIsError()
    {
        return $this->getData('is_error');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->getData('result');
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * @param string $sid
     * @return $this
     */
    public function setSid($sid)
    {
        return $this->setData('sid', $sid);
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * @param string $_message
     * @return $this
     */
    public function setMsg($_message)
    {
        return $this->setData('msg', $_message);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail($email)
    {
        return $this->setData('customer_email', $email);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setCustomerName($name)
    {
        return $this->setData('customer_name', $name);
    }

    /**
     * @param int $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        return $this->setData('entity_type_id', $entityTypeId);
    }

    /**
     * @param string $recipientPhone
     * @return $this
     */
    public function setRecipientPhone($recipientPhone)
    {
        return $this->setData('recipient_phone', $recipientPhone);
    }

    /**
     * @param int|boolean $isError
     * @return $this
     */
    public function setIsError($isError)
    {
        return $this->setData('is_error', $isError);
    }

    /**
     * @param string $result
     * @return $this
     */
    public function setResult($result)
    {
        return $this->setData('result', $result);
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData('updated_at', $updatedAt);
    }
}
