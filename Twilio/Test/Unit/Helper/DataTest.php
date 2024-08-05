<?php
 

namespace Kitchen365\Twilio\Test\Unit\Helper;

use Kitchen365\Twilio\Helper\Data as Helper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Framework\App\Config\ScopeConfigInterface;

/** @codeCoverageIgnore */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|MockObject */
    protected $scopeConfigMock;

    /** @var \Kitchen365\Twilio\Helper\Data */
    protected $helper;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scopeConfigMock = $this->getMockForAbstractClass(
            ScopeConfigInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getValue']
        );

        $this->helper = $objectManager->getObject(
            Helper::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    /**
     * @dataProvider testIsTwilioEnabledDataProvider
     * @param bool $result
     */
    public function testIsTwilioEnabled($result)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn($result);

        $this->assertEquals(
            $result,
            $this->helper->isTwilioEnabled()
        );
    }

    public function testIsTwilioEnabledDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider testIsMessageEnabledDataProvider
     * @param bool $moduleEnabled
     * @param bool $messageEnabled
     * @param bool $expectedResult
     */
    public function testIsOrderMessageEnabled(
        $moduleEnabled,
        $messageEnabled,
        $expectedResult
    ) {
        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->willReturn($messageEnabled);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->willReturn($moduleEnabled);

        $this->assertEquals(
            $expectedResult,
            $this->helper->isOrderMessageEnabled()
        );
    }

    /**
     * @dataProvider testIsMessageEnabledDataProvider
     * @param bool $moduleEnabled
     * @param bool $messageEnabled
     * @param bool $expectedResult
     */
    public function testIsInvoiceMessageEnabled(
        $moduleEnabled,
        $messageEnabled,
        $expectedResult
    ) {
        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->willReturn($messageEnabled);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->willReturn($moduleEnabled);

        $this->assertEquals(
            $expectedResult,
            $this->helper->isInvoiceMessageEnabled()
        );
    }

    /**
     * @dataProvider testIsMessageEnabledDataProvider
     * @param bool $moduleEnabled
     * @param bool $messageEnabled
     * @param bool $expectedResult
     */
    public function testIsShipmentMessageEnabled(
        $moduleEnabled,
        $messageEnabled,
        $expectedResult
    ) {
        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->willReturn($messageEnabled);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->willReturn($moduleEnabled);

        $this->assertEquals(
            $expectedResult,
            $this->helper->isShipmentMessageEnabled()
        );
    }

    public function testIsMessageEnabledDataProvider()
    {
        return [
            [true, true, true],
            [false, true, false]
        ];
    }
}
