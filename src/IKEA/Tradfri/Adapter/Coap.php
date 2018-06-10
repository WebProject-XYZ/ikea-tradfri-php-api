<?php

declare(strict_types=1);

namespace IKEA\Tradfri\Adapter;

use IKEA\Tradfri\Collection\Devices;
use IKEA\Tradfri\Collection\Groups;
use IKEA\Tradfri\Command\Coaps;
use IKEA\Tradfri\Exception\RuntimeException;
use IKEA\Tradfri\Group\Light;
use IKEA\Tradfri\Helper\CoapCommandKeys;
use IKEA\Tradfri\Helper\Online;
use IKEA\Tradfri\Helper\Runner;
use IKEA\Tradfri\Mapper\MapperInterface;
use IKEA\Tradfri\Service\ServiceInterface;

/**
 * Class Coap.
 */
class Coap extends AdapterAbstract
{
    const COULD_NOT_SWITCH_STATE = 'Could not switch state';
    /**
     * @var Coaps
     */
    protected $_commands;

    /**
     * @var bool
     */
    protected $_isOnline = false;

    /**
     * Coap constructor.
     *
     * @param Coaps           $commands
     * @param MapperInterface $deviceDataMapper
     * @param MapperInterface $groupDataMapper
     */
    public function __construct(
        Coaps $commands,
        MapperInterface $deviceDataMapper,
        MapperInterface $groupDataMapper
    ) {
        $this->_commands = $commands;

        $this->checkOnline($commands->getIp());

        parent::__construct($deviceDataMapper, $groupDataMapper);
    }

    /**
     * Check online state.
     *
     * @deprecated no more ping from gateway
     *
     * @param string $ipAddress
     *
     * @return bool
     */
    public function checkOnline(string $ipAddress): bool
    {
        // disabled
        $state = true;
        $this->setOnline(true);

        return $state;
    }

    /**
     * Set IsOnline.
     *
     * @param bool $isOnline
     *
     * @return Coap
     */
    public function setOnline(bool $isOnline): self
    {
        $this->_isOnline = $isOnline;

        return $this;
    }

    /**
     * Get device type.
     *
     * @param int $deviceId
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return string
     */
    public function getType(int $deviceId): string
    {
        $data = $this->_getData(CoapCommandKeys::KEY_GET_DATA, $deviceId);
        if (\is_object($data)
            && \property_exists($data, CoapCommandKeys::KEY_DATA)
            && \property_exists($data, CoapCommandKeys::KEY_TYPE)
        ) {
            return $data
                ->{CoapCommandKeys::KEY_DATA}
                ->{CoapCommandKeys::KEY_TYPE};
        }

        throw new RuntimeException('invalid coap response');
    }

    /**
     * Get data from coaps client.
     *
     * @param string   $requestType
     * @param null|int $deviceId
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return array|\stdClass|string
     */
    protected function _getData(string $requestType, int $deviceId = null)
    {
        if ($this->isOnline()) {
            $command = $this->_commands->getCoapsCommandGet($requestType);

            if (null !== $deviceId) {
                $command .= '/'.$deviceId;
            }

            $dataRaw = $this->_commands->parseResult(
                Runner::execWithTimeout($command, 1)
            );

            if (false !== $dataRaw) {
                $decoded = \json_decode($dataRaw);
                if (null === $decoded) {
                    $decoded = $dataRaw;
                }

                return $decoded;
            }

            throw new RuntimeException('invalid hub response');
        }

        throw new RuntimeException('api is offline');
    }

    /**
     * Get isOnline.
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->_isOnline;
    }

    /**
     * Get Manufacturer by device id.
     *
     * @param int $deviceId
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return string
     */
    public function getManufacturer(int $deviceId): string
    {
        $data = $this->_getData(CoapCommandKeys::KEY_GET_DATA, $deviceId);

        if (isset($data->{CoapCommandKeys::KEY_DATA}->{'0'})) {
            return $data->{CoapCommandKeys::KEY_DATA}->{'0'};
        }

        throw new RuntimeException('invalid hub response');
    }

    /**
     * Change State of given device.
     *
     * @param int  $deviceId
     * @param bool $toState
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return bool
     */
    public function changeLightState(int $deviceId, bool $toState): bool
    {
        // get command to switch light
        $onCommand = $this->_commands
            ->getLightSwitchCommand($deviceId, $toState);

        // run command
        $data = Runner::execWithTimeout(
            $onCommand,
            2,
            true,
            true
        );
        // verify result
        if (\is_array($data) && empty($data[0])) {
            /*
             * @example data response is now empty since hub update
             * so we only check if there is no error message inside
             */
            return true;
        }

        throw new RuntimeException(self::COULD_NOT_SWITCH_STATE);
    }

    /**
     * Change state of group.
     *
     * @param int  $groupId
     * @param bool $toState
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return bool
     */
    public function changeGroupState(int $groupId, bool $toState): bool
    {
        // get command to switch light
        $onCommand = $this->_commands
            ->getGroupSwitchCommand($groupId, $toState);

        // run command
        $data = Runner::execWithTimeout($onCommand, 2);

        // verify result
        if (\is_array($data) && 4 === \count($data)) {
            return true;
        }

        throw new RuntimeException(self::COULD_NOT_SWITCH_STATE);
    }

    /**
     * Set Light Brightness.
     *
     * @param int $lightId
     * @param int $level
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return bool
     */
    public function setLightBrightness(int $lightId, int $level): bool
    {
        // get command to dim light
        $onCommand = $this->_commands
            ->getLightDimmerCommand($lightId, $level);

        // run command
        $data = Runner::execWithTimeout($onCommand, 2);

        // verify result
        if (\is_array($data) && 4 === \count($data)) {
            return true;
        }

        throw new RuntimeException(self::COULD_NOT_SWITCH_STATE);
    }

    /**
     * Set Group Brightness.
     *
     * @param int $groupId
     * @param int $level
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return bool
     */
    public function setGroupBrightness(int $groupId, int $level): bool
    {
        // get command to switch light
        $onCommand = $this->_commands->getGroupDimmerCommand($groupId, $level);

        // run command
        $data = Runner::execWithTimeout($onCommand, 2);

        // verify result
        if (\is_array($data) && 4 === \count($data)) {
            return true;
        }

        throw new RuntimeException(self::COULD_NOT_SWITCH_STATE);
    }

    /**
     * Get a collection of devices.
     *
     * @param ServiceInterface $service
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return Devices
     */
    public function getDeviceCollection(ServiceInterface $service): Devices
    {
        return $this
            ->getDeviceDataMapper()
            ->map($service, $this->getDevicesData());
    }

    /**
     * Get devices.
     *
     * @param null|array $deviceIds
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return array
     */
    public function getDevicesData(array $deviceIds = null): array
    {
        if (null === $deviceIds) {
            $deviceIds = $this->getDeviceIds();
        }
        $deviceData = [];
        foreach ($deviceIds as $deviceId) {
            // sometimes the request are to fast,
            // the hub will decline the request (flood security)
            $deviceData[$deviceId] = $this->getDeviceData((int) $deviceId);
            // @TODO: add command queue
            if ((int) COAP_GATEWAY_FLOOD_PROTECTION > 0) {
                \usleep((int) COAP_GATEWAY_FLOOD_PROTECTION);
            }
        }

        return $deviceData;
    }

    /**
     * Get an array with lamp ids from soap client.
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return array
     */
    public function getDeviceIds(): array
    {
        return $this->_getData(CoapCommandKeys::KEY_GET_DATA);
    }

    /**
     * Get Device data.
     *
     * @param int $deviceId
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return \stdClass
     */
    public function getDeviceData(int $deviceId): \stdClass
    {
        return $this->_getData(CoapCommandKeys::KEY_GET_DATA, $deviceId);
    }

    /**
     * Get a collection of Groups.
     *
     * @param ServiceInterface $service
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return Groups
     */
    public function getGroupCollection(ServiceInterface $service): Groups
    {
        $groups = $this
            ->getGroupDataMapper()
            ->map($service, $this->getGroupsData());

        if (false === $groups->isEmpty()) {
            foreach ($groups->toArray() as $group) {
                /** @var Light $group */
                $groupDevices = $this
                    ->_deviceDataMapper
                    ->map(
                        $service,
                        $this->getDevicesData($group->getDeviceIds())
                    );
                $group->setDevices($groupDevices);
            }
        }

        return $groups;
    }

    /**
     * Get Groups from hub.
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return array
     */
    public function getGroupsData(): array
    {
        $groupData = [];
        foreach ($this->getGroupIds() as $groupId) {
            // sometimes the request are to fast,
            // the hub will decline the request (flood security)
            $groupData[$groupId] = $this->_getData(
                CoapCommandKeys::KEY_GET_GROUPS,
                $groupId
            );
        }

        return $groupData;
    }

    /**
     * Get Group data from hub.
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     *
     * @return array
     */
    public function getGroupIds(): array
    {
        return $this->_getData(CoapCommandKeys::KEY_GET_GROUPS);
    }
}
