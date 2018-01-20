<?php

declare(strict_types=1);

namespace IKEA\Tradfri\Device;

/**
 * Class Remote.
 */
class Remote extends Device
{
    /**
     * Remote constructor.
     *
     * @param int $deviceId
     *
     * @throws \IKEA\Tradfri\Exception\RuntimeException
     */
    public function __construct($deviceId)
    {
        parent::__construct($deviceId, self::TYPE_REMOTE_CONTROL);
    }
}
