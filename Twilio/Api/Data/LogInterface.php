<?php

namespace Kitchen365\Twilio\Api\Data;

interface LogInterface
{
    public function getId();

    public function getSid();

    public function getMsg();

    public function getCustomerEmail();

    public function getCustomerName();

    public function getEntityId();

    public function getEntityTypeId();

    public function getRecipientPhone();

    public function getIsError();

    public function getResult();

    public function getCreatedAt();

    public function getUpdatedAt();

    public function setId($id);

    public function setSid($sid);

    public function setMsg($_message);

    public function setCustomerEmail($email);

    public function setCustomerName($name);

    public function setEntityId($entityId);

    public function setEntityTypeId($entityTypeId);

    public function setRecipientPhone($recipientPhone);

    public function setIsError($isError);

    public function setResult($result);

    public function setUpdatedAt($updatedAt);
}
