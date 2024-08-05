<?php
 

namespace Kitchen365\Twilio\Test\Unit\Plugin\Sales\Block\Adminhtml\Order\Invoice;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Kitchen365\Twilio\Plugin\Sales\Block\Adminhtml\Order\Invoice\View as ViewPlugin;
use Kitchen365\Twilio\Helper\Data;
use Magento\Sales\Block\Adminhtml\Order\Invoice\View as InvoiceView;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Model\Order\Invoice;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kitchen365\Twilio\Plugin\Sales\Block\Adminhtml\Order\Invoice\View */
    protected $viewPlugin;

    /** @var \Kitchen365\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Sales\Block\Adminhtml\Order\Invoice\View|MockObject */
    protected $invoiceViewMock;

    /** @var \Magento\Framework\AuthorizationInterface|MockObject */
    protected $authorizationMock;

    /** @var \Magento\Sales\Model\Order\Invoice|MockObject */
    protected $invoiceMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['isInvoiceMessageEnabled'])
            ->getMock();

        $this->invoiceViewMock = $this->getMockBuilder(InvoiceView::class)
            ->disableOriginalConstructor()
            ->setMethods(['addButton', 'getInvoice'])
            ->getMock();

        $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAllowed'])
            ->getMockForAbstractClass();

        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->invoiceViewMock->expects($this->any())
            ->method('getInvoice')
            ->willReturn($this->invoiceMock);

        $this->viewPlugin = $objectManager->getObject(
            ViewPlugin::class,
            [
                '_helper' => $this->helperMock,
                '_authorization' => $this->authorizationMock,
            ]
        );
    }

    public function testBeforeSetLayout()
    {
        $this->helperMock->expects($this->any())
            ->method('isInvoiceMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(true);

        $this->invoiceViewMock->expects($this->once())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->invoiceViewMock);
    }

    public function testBeforeSetLayoutWithoutAccess()
    {
        $this->helperMock->expects($this->any())
            ->method('isInvoiceMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(false);

        $this->invoiceViewMock->expects($this->never())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->invoiceViewMock);
    }
}
