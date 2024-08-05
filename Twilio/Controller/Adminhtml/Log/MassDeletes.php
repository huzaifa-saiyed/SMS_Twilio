<?php

namespace Kitchen365\Twilio\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Kitchen365\Twilio\Model\LogRepository;
use Kitchen365\Twilio\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassDeletes extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Kitchen365_Twilio::customer_sms';

    protected $_filter;

    protected $_logRepository;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        LogRepository $logRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_logRepository = $logRepository;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $count = $collection->getSize();

        foreach ($collection->getAllIds() as $logId) {
            $this->_logRepository->delete($logId);
        }

        $this->messageManager->addSuccessMessage($count . __(' log item(s) have been deleted.'));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('twilio/customer/');
    }
}
