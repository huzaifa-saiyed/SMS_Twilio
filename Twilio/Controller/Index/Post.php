<?php
namespace Kitchen365\Twilio\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Twilio\Rest\Client;
use Kitchen365\Twilio\Model\LogFactory;
use Kitchen365\Twilio\Model\LogRepository;
use Kitchen365\Twilio\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

class Post extends Action
{
    protected $formKeyValidator;
    protected $resultRedirectFactory;
    protected $scopeConfig;
    protected $logFactory;
    protected $logRepository;
    protected $logger;

    /**
     * @var \Kitchen365\Twilio\Helper\Data
     */
    protected $_helper;

     /**
     * @var int
     */
    protected $entityTypeId = 4;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        RedirectFactory $resultRedirectFactory,
        ScopeConfigInterface $scopeConfig,
        LogFactory $logFactory,
        LogRepository $logRepository,
        Helper $helper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logFactory = $logFactory;
        $this->logRepository = $logRepository;
        $this->logger = $logger;
        $this->_helper = $helper;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        // Set the email and name properties
        $this->email = $post['email'] ?? null;
        $this->name = $post['name'] ?? null;

        try {
            if ($this->_helper->isTwilioEnabled() && $this->_helper->isContactUsMessageEnabled()) {
               
                $twilioSid = $this->scopeConfig->getValue('sales_sms/general/account_sid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $twilioToken = $this->scopeConfig->getValue('sales_sms/general/auth_token', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $twilioNumber = $this->scopeConfig->getValue('sales_sms/general/twilio_phone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                // Send SMS
                $client = new Client($twilioSid, $twilioToken);
                $message = $client->messages->create(
                    $post['telephone'], // to
                    [
                        'from' => $twilioNumber,
                        'body' => 'Thank You for contacting us!'
                    ]
                );

                if ($this->_helper->isLogEnabled()) {
                    $this->logSuccess($this->_helper->getContactUsMessage(), $post['telephone'], $message->sid);
                }

            $this->messageManager->addSuccess(__('Thanks for contacting us. We\'ll respond to you very soon.'));

            } else {
                $this->messageManager->addSuccess(__('Thanks for contacting us. We\'ll respond to you very soon.'));
            } 
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            if ($this->_helper->isLogEnabled()) {
                $this->logError($post['comment'], $post['telephone']);
            }
            $this->messageManager->addError(__('We can\'t process your request right now. Please try again later.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    protected function logSuccess($message, $phone, $sid)
    {
        $log = $this->logFactory->create();

        $log->setMsg($message);
        $log->setEntityTypeId($this->entityTypeId);
        $log->setCustomerEmail($this->email);
        $log->setCustomerName($this->name);
        $log->setRecipientPhone($phone);
        $log->setIsError(false);
        $log->setResult('Sent Successfully');
        $log->setSid($sid);

        $this->logRepository->save($log);
    }

    protected function logError($message, $phone)
    {
        $log = $this->logFactory->create();

        $log->setMsg($message);
        $log->setEntityTypeId($this->entityTypeId);
        $log->setCustomerEmail($this->email);
        $log->setCustomerName($this->name);
        $log->setRecipientPhone($phone);
        $log->setIsError(true);
        $log->setResult('Failed');
        $log->setSid(null);

        $this->logRepository->save($log);
    }
}
