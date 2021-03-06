<?php
declare(strict_types=1);

namespace IKEA\Tests\Tradfri\Device\Helper;

use IKEA\Tradfri\Command\Coap\Keys;
use IKEA\Tradfri\Device\Helper\Type;

/**
 * Class TypeTest
 */
class TypeTest extends \Codeception\Test\Unit
{
    /**
     * @param string $typeAttribute
     * @param bool   $assertTrue
     * @dataProvider isLightBulbData
     */
    public function testIsLightBulb(
        string $typeAttribute,
        bool $assertTrue
    ): void {
        // Arrange
        $helper = new Type();

        // Act
        $condition = $helper->isLightBulb($typeAttribute);
        // Assert
        if ($assertTrue) {
            $this->assertTrue(
                $condition,
                'Type: '.$typeAttribute
            );
        } else {
            $this->assertFalse(
                $condition,
                'Type: '.$typeAttribute
            );
        }
    }

    /**
     * @return array
     */
    public function isLightBulbData(): array
    {
        return [
            ['invalidStringValue', false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_W, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_WS, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_WS, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_W, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_MOTION_SENSOR, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_REMOTE_CONTROL, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_DIMMER, false],
        ];
    }

    /**
     * @param string $typeAttribute
     * @param bool   $assertTrue
     * @dataProvider isDimmerData
     */
    public function testIsDimmer(
        string $typeAttribute,
        bool $assertTrue
    ): void {
        // Arrange
        $helper = new Type();

        // Act
        $condition = $helper->isDimmer($typeAttribute);
        // Assert
        if ($assertTrue) {
            $this->assertTrue(
                $condition,
                'Type: '.$typeAttribute
            );
        } else {
            $this->assertFalse(
                $condition,
                'Type: '.$typeAttribute
            );
        }
    }

    /**
     * @return array
     */
    public function isDimmerData(): array
    {
        return [
            ['invalidStringValue', false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_MOTION_SENSOR, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_REMOTE_CONTROL, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_DIMMER, true],
        ];
    }

    /**
     * @param string $typeAttribute
     * @param bool   $assertTrue
     * @dataProvider isRemoteData
     */
    public function testIsRemote(
        string $typeAttribute,
        bool $assertTrue
    ): void {
        // Arrange
        $helper = new Type();

        // Act
        $condition = $helper->isRemote($typeAttribute);
        // Assert
        if ($assertTrue) {
            $this->assertTrue(
                $condition,
                'Type: '.$typeAttribute
            );
        } else {
            $this->assertFalse(
                $condition,
                'Type: '.$typeAttribute
            );
        }
    }

    /**
     * @return array
     */
    public function isRemoteData(): array
    {
        return [
            ['invalidStringValue', false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_MOTION_SENSOR, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_REMOTE_CONTROL, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_DIMMER, false],
        ];
    }

    /**
     * @param string $typeAttribute
     * @param bool   $assertTrue
     * @dataProvider isMotionSensorData
     */
    public function testIsMotionSensor(
        string $typeAttribute,
        bool $assertTrue
    ): void {
        // Arrange
        $helper = new Type();

        // Act
        $condition = $helper->isMotionSensor($typeAttribute);
        // Assert
        if ($assertTrue) {
            $this->assertTrue(
                $condition,
                'Type: '.$typeAttribute
            );
        } else {
            $this->assertFalse(
                $condition,
                'Type: '.$typeAttribute
            );
        }
    }

    /**
     * @return array
     */
    public function isMotionSensorData(): array
    {
        return [
            ['invalidStringValue', false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_WS, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_W, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_MOTION_SENSOR, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_REMOTE_CONTROL, false],
            [Keys::ATTR_DEVICE_INFO_TYPE_DIMMER, false],
        ];
    }

    /**
     * @param string $typeAttribute
     * @param bool   $assertTrue
     * @dataProvider isKnownDeviceTypeData
     */
    public function testKnownDeviceType(
        string $typeAttribute,
        bool $assertTrue
    ): void {
        // Arrange
        $helper = new Type();

        // Act
        $condition = $helper->isKnownDeviceType($typeAttribute);
        // Assert
        if ($assertTrue) {
            $this->assertTrue(
                $condition,
                'Type: '.$typeAttribute
            );
        } else {
            $this->assertFalse(
                $condition,
                'Type: '.$typeAttribute
            );
        }
    }

    /**
     * @return array
     */
    public function isKnownDeviceTypeData(): array
    {
        return [
            ['invalidStringValue', false],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_W, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_E27_WS, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_WS, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_BLUB_GU10_W, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_MOTION_SENSOR, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_REMOTE_CONTROL, true],
            [Keys::ATTR_DEVICE_INFO_TYPE_DIMMER, true],
        ];
    }
}
