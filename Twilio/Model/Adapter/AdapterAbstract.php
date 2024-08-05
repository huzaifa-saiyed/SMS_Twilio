<?php

namespace Kitchen365\Twilio\Model\Adapter;

use Kitchen365\Twilio\Helper\Data as Helper;
use Kitchen365\Twilio\Helper\MessageTemplateParser;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Twilio\Rest\Client as TwilioClient;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Kitchen365\Twilio\Model\LogFactory;
use Magento\Framework\UrlInterface;

abstract class AdapterAbstract
{
    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Twilio\Rest\ClientFactory
     */
    protected $_twilioClientFactory;

    /**
     * @var string
     */
    protected $_message;

    /**
     * @var string
     */
    protected $_recipientPhone;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Kitchen365\Twilio\Helper\MessageTemplateParser
     */
    protected $_messageTemplateParser;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Kitchen365\Twilio\Model\LogRepository
     */
    protected $_twilioLogRepository;

    /**
     * @var LogFactory
     */
    protected $_twilioLogFactory;

    /**
     * @var int
     */
    protected $entityTypeId = 0;

    /**
     * @var int
     */
    protected $entityId;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * AdapterAbstract constructor.
     * @param Helper $helper
     * @param TwilioClientFactory $twilioClientFactory
     * @param LoggerInterface $logger
     * @param MessageTemplateParser $messageTemplateParser
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $url
     */
    public function __construct(
        Helper $helper,
        TwilioClientFactory $twilioClientFactory,
        LoggerInterface $logger,
        MessageTemplateParser $messageTemplateParser,
        StoreManagerInterface $storeManager,
        \Kitchen365\Twilio\Model\LogRepository $logRepository,
        \Kitchen365\Twilio\Model\LogFactory $logFactory,
        UrlInterface $url
    ) {
        $this->_helper = $helper;
        $this->_twilioClientFactory = $twilioClientFactory;
        $this->_logger = $logger;
        $this->_messageTemplateParser = $messageTemplateParser;
        $this->_storeManager = $storeManager;
        $this->_twilioLogRepository = $logRepository;
        $this->_twilioLogFactory = $logFactory;
        $this->urlBuilder = $url;
    }

    /**
     * @return $this
     */
    protected function _sendSms()
    {
        $client = $this->_twilioClientFactory->create([
            'username' => $this->_helper->getAccountSid(),
            'password' => $this->_helper->getAccountAuthToken()
        ]);

        try {
            $result = $client->messages->create(
                $this->_recipientPhone,
                [
                    'from' => $this->_helper->getTwilioPhone(),
                    'body' => $this->_message,
                    'statusCallback' => $this->urlBuilder->getUrl('twilio/webhook'),
                ]
            );

            $this->logSuccess($result);
        } catch (\Exception $e) {
            $this->logError($e);
        }

        return $this;
    }

    /**
     * @param \Twilio\Rest\Api\V2010\Account\MessageInstance $result
     */
    protected function logSuccess($result)
    {
        $this->_logResult('Sent Successfully', $result->sid);
    }

    /**
     * @param \Exception $exception
     */
    protected function logError($exception)
    {
        $this->_logResult('Failed', null, true);
    }

    /**
     * @param string $status
     * @param null|string $sid
     * @param bool $error
     */
    protected function _logResult($status, $sid = null, $error = false)
    {
        if (!$this->_helper->isLogEnabled()) {
            return;
        }

        $log = $this->_twilioLogFactory->create();

        $log->setEntityId($this->entityId);
        $log->setMsg($this->_message);
        $log->setCustomerEmail($this->email);
        $log->setCustomerName($this->name);
        $log->setEntityTypeId($this->entityTypeId);
        $log->setRecipientPhone($this->_recipientPhone);
        $log->setIsError($error);
        $log->setResult($status);
        $log->setSid($sid);

        $this->_twilioLogRepository->save($log);
    }
}
