<?php

namespace Kitchen365\Twilio\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface LogRepositoryInterface
{
    public function save(\Kitchen365\Twilio\Api\Data\LogInterface $log);

    /**
     * @param string|int $logId
     * @throws NoSuchEntityException
     * @return Data\LogInterface
     */
    public function getById($logId);

    /**
     * @param string $sid
     * @throws NoSuchEntityException
     * @return Data\LogInterface
     */
    public function getBySid($sid);

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    public function delete($logId);
}
