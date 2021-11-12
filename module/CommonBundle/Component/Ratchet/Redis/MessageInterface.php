<?php

namespace CommonBundle\Component\Ratchet\Redis;

interface MessageInterface
{
    /**
     * @param string $channel
     * @param string $payload
     */
    public function onRedisMessage($channel, $payload);
}
