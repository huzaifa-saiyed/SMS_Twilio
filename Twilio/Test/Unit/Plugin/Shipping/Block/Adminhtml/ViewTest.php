<?php
 

namespace Kitchen365\Twilio\Test\Unit\Plugin\Shipping\Block\Adminhtml;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Kitchen365\Twilio\Plugin\Shipping\Block\Adminhtml\View as ViewPlugin;
use Kitchen365\Twilio\Helper\Data;
use Magento\Shipping\Block\Adminhtml\View as ShipmentView;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Model\Order\Shipment;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kitchen365\Twilio\Plugin\Shipping\Block\Adminhtml\View */
    protected $viewPlugin;

    /** @var \Kitchen365\Twilio\Helper\Data|MockObject */
    protected $helperMock;

    /** @var \Magento\Shipping\Block\Adminhtml\View|MockObject */
    protected $shipmentViewMock;

    /** @var \Magento\Framework\AuthorizationInterface|MockObject */
    protected $authorizationMock;

    /** @var \Magento\Sales\Model\Order\Shipment|MockObject */
    protected $shipmentMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->helperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['isShipmentMessageEnabled'])
            ->getMock();

        $this->shipmentViewMock = $this->getMockBuilder(ShipmentView::class)
            ->disableOriginalConstructor()
            ->setMethods(['addButton', 'getShipment'])
            ->getMock();

        $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAllowed'])
            ->getMockForAbstractClass();

        $this->shipmentMock = $this->getMockBuilder(Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->shipmentViewMock->expects($this->any())
            ->method('getShipment')
            ->willReturn($this->shipmentMock);

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
            ->method('isShipmentMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(true);

        $this->shipmentViewMock->expects($this->once())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->shipmentViewMock);
    }

    public function testBeforeSetLayoutWithoutAccess()
    {
        $this->helperMock->expects($this->any())
            ->method('isShipmentMessageEnabled')
            ->willReturn(true);

        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->willReturn(false);

        $this->shipmentViewMock->expects($this->never())
            ->method('addButton')
            ->willReturnSelf();

        $this->viewPlugin->beforeSetLayout($this->shipmentViewMock);
    }
}
