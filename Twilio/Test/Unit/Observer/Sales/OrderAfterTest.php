<?php
 

namespace Kitchen365\Twilio\Test\Unit\Observer\Sales;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Kitchen365\Twilio\Observer\Sales\OrderAfter;
use Magento\Framework\Event\Observer;
use Kitchen365\Twilio\Model\Adapter\Order as OrderAdapter;
use Kitchen365\Twilio\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;

/** @codeCoverageIgnore */
class OrderAfterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kitchen365\Twilio\Observer\Sales\OrderAfter */
    protected $orderAfter;

    /** @var \Magento\Framework\Event\Observer|MockObject */
    protected $observerMock;

    /** @var \Kitchen365\Twilio\Model\Adapter\Order|MockObject */
    protected $orderAdapterMock;

    /** @var \Kitchen365\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Sales\Model\Order|MockObject */
    protected $orderMock;

    /** @var \Magento\Sales\Model\Order\Address|MockObject */
    protected $addressMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isTwilioEnabled'])
            ->getMock();

        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder'])
            ->getMock();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBillingAddress'])
            ->getMock();

        $this->addressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSmsAlert'])
            ->getMock();

        $this->orderAdapterMock = $this->getMockBuilder(OrderAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendOrderSms'])
            ->getMock();

        $this->orderAfter = $objectManager->getObject(
            OrderAfter::class,
            [
                '_helper' => $this->helperMock,
                '_orderAdapter' => $this->orderAdapterMock
            ]
        );
    }

    public function testExecute()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(true);

        $this->orderAdapterMock->expects($this->once())
            ->method('sendOrderSms')
            ->with($this->orderMock)
            ->willReturnSelf();

        $this->orderAfter->execute($this->observerMock);
    }

    public function testExecuteWithSmsOptOut()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(false);

        $this->orderAdapterMock->expects($this->never())
            ->method('sendOrderSms');

        $this->orderAfter->execute($this->observerMock);
    }

    public function testExecuteWithModuleDisabled()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(false);

        $this->observerMock->expects($this->never())
            ->method('getOrder');

        $this->orderAfter->execute($this->observerMock);
    }
}
