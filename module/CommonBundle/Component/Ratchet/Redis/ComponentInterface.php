<?php

namespace CommonBundle\Component\Ratchet\Redis;

use Exception;

interface ComponentInterface
{
    public function onRedisClose();

    /**
     * @param Exception $e
     */
    public function onRedisError(Exception $e);
}
