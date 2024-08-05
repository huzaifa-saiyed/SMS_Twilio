<?php
 

namespace Kitchen365\Twilio\Test\Unit\Observer\Sales\Order\Shipment;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Kitchen365\Twilio\Observer\Sales\Order\Shipment\SaveAfter;
use Magento\Framework\Event\Observer;
use Kitchen365\Twilio\Model\Adapter\Order\Shipment as ShipmentAdapter;
use Kitchen365\Twilio\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;

/** @codeCoverageIgnore */
class SaveAfterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kitchen365\Twilio\Observer\Sales\Order\Shipment\SaveAfter */
    protected $saveAfter;

    /** @var \Magento\Framework\Event\Observer|MockObject */
    protected $observerMock;

    /** @var \Kitchen365\Twilio\Model\Adapter\Order\Shipment|MockObject */
    protected $shipmentAdapterMock;

    /** @var \Kitchen365\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Sales\Model\Order|MockObject */
    protected $orderMock;

    /** @var \Magento\Sales\Model\Order\Address|MockObject */
    protected $addressMock;

    /** @var \Magento\Sales\Model\Order\Shipment|MockObject */
    protected $shipmentMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isTwilioEnabled'])
            ->getMock();

        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipment'])
            ->getMock();

        $this->shipmentMock = $this->getMockBuilder(Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder'])
            ->getMock();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingAddress'])
            ->getMock();

        $this->addressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSmsAlert'])
            ->getMock();

        $this->shipmentAdapterMock = $this->getMockBuilder(ShipmentAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendOrderSms'])
            ->getMock();

        $this->saveAfter = $objectManager->getObject(
            SaveAfter::class,
            [
                '_helper' => $this->helperMock,
                '_shipmentAdapter' => $this->shipmentAdapterMock
            ]
        );
    }

    public function testExecute()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getShipment')
            ->willReturn($this->shipmentMock);

        $this->shipmentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(true);

        $this->shipmentAdapterMock->expects($this->once())
            ->method('sendOrderSms')
            ->with($this->shipmentMock)
            ->willReturnSelf();

        $this->saveAfter->execute($this->observerMock);
    }

    public function testExecuteWithSmsOptOut()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getShipment')
            ->willReturn($this->shipmentMock);

        $this->shipmentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getSmsAlert')
            ->willReturn(false);

        $this->shipmentAdapterMock->expects($this->never())
            ->method('sendOrderSms');

        $this->saveAfter->execute($this->observerMock);
    }

    public function testExecuteWithoutShippingAddress()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(true);

        $this->observerMock->expects($this->once())
            ->method('getShipment')
            ->willReturn($this->shipmentMock);

        $this->shipmentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn(null);

        $this->addressMock->expects($this->never())
            ->method('getSmsAlert');

        $this->shipmentAdapterMock->expects($this->never())
            ->method('sendOrderSms');

        $this->saveAfter->execute($this->observerMock);
    }

    public function testExecuteWithModuleDisabled()
    {
        $this->helperMock->expects($this->once())
            ->method('isTwilioEnabled')
            ->willReturn(false);

        $this->observerMock->expects($this->never())
            ->method('getShipment');

        $this->saveAfter->execute($this->observerMock);
    }
}
