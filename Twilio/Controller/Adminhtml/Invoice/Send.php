<?php

namespace Kitchen365\Twilio\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Kitchen365\Twilio\Model\Adapter\Order\Invoice as InvoiceAdapter;

class Send extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Kitchen365_Twilio::sms';

    /** @var \Magento\Sales\Api\InvoiceRepositoryInterface */
    protected $_invoiceRepository;

    /** @var \Kitchen365\Twilio\Model\Adapter\Order\Invoice */
    protected $_invoiceAdapter;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceAdapter $invoiceAdapter,
        Context $context
    ) {
        parent::__construct($context);
        $this->_invoiceAdapter = $invoiceAdapter;
        $this->_invoiceRepository = $invoiceRepository;
    }

    public function execute()
    {
        $invoice = $this->_initInvoice();
        if ($invoice) {
            $resultRedirect = $this->resultRedirectFactory->create()->setPath(
                'sales/invoice/view',
                [
                    'invoice_id' => $invoice->getEntityId(),
                    'customer_email' => $order->getCustomerEmail(),
                    'customer_name' => $order->getCustomerFirstname()
                ]
            );

            $order = $invoice->getOrder();

            $billingAddress = $order->getBillingAddress();

            if ($billingAddress->getSmsAlert()) {
                $result = $this->_invoiceAdapter->sendOrderSms($invoice);
                $this->messageManager->addSuccessMessage(__('The SMS has been sent.'));

                return $resultRedirect;
            }

            $this->messageManager->addErrorMessage(__('The billing telephone number did not opt-in for SMS notifications.'));

            return $resultRedirect;
        }

        return $this->resultRedirectFactory->create()->setPath('sales/invoice/*');
    }

    /**
     * @return false|\Magento\Sales\Model\Order\Invoice
     */
    protected function _initInvoice()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $invoice = $this->_invoiceRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This invoice no longer exists.'));
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This invoice no longer exists.'));
            return false;
        }

        return $invoice;
    }
}
