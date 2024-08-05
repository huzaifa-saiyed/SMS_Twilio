<?php
 

namespace Kitchen365\Twilio\Test\Unit\Plugin\Sales\Block\Adminhtml\Order;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Kitchen365\Twilio\Plugin\Sales\Block\Adminhtml\Order\View as ViewPlugin;
use Kitchen365\Twilio\Helper\Data;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Model\Order;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kitchen365\Twilio\Plugin\Sales\Block\Adminhtml\Order\View */
    protected $viewPlugin;

    /** @var \Kitchen365\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Sales\Block\Adminhtml\Order\View|MockObject */
    protected $orderViewMock;

    /** @var \Magento\Framework\AuthorizationInterface|MockObject */
    protected $authorizationMock;

    /** @var \Magento\Sales\Model\Order|MockObject */
    protected $orderMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOrderMessageEnabled'])
            ->getMock();

        $this->orderViewMock = $this->getMockBuilder(OrderView::class)
            ->disableOriginalConstructor()
            ->setMethods(['addButton', 'getOrderId', 'getOrder'])
            ->getMock();

        $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAllowed'])
            ->getMockForAbstractClass();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['isCanceled'])
            ->getMock();

        $this->orderViewMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($this->orderMock);

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
            ->method('isOrderMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(true);

        $this->orderMock->expects($this->any())
            ->method('isCanceled')
            ->willReturn(false);

        $this->orderViewMock->expects($this->once())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->orderViewMock);
    }

    public function testBeforeSetLayoutWithoutAccess()
    {
        $this->helperMock->expects($this->any())
            ->method('isOrderMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(false);

        $this->orderMock->expects($this->any())
            ->method('isCanceled')
            ->willReturn(false);

        $this->orderViewMock->expects($this->never())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->orderViewMock);
    }

    public function testBeforeSetLayoutWithCanceledOrder()
    {
        $this->helperMock->expects($this->any())
            ->method('isOrderMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(true);

        $this->orderMock->expects($this->any())
            ->method('isCanceled')
            ->willReturn(true);

        $this->orderViewMock->expects($this->never())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->orderViewMock);
    }
}
