<?php

namespace CommonBundle\Component\Redis;

use Credis_Client;
use Redis;
use RuntimeException;

class Client
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Credis_Client
     */
    private $credisClient;

    /**
     * @param  array $config
     * @return self
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->credisClient = new Credis_Client(
            $config['host'],
            $config['port'],
            $config['timeout'],
            $config['persistent_id'],
            $config['database'],
            $config['password']
        );

        foreach ($config['lib_options'] as $key => $value) {
            $this->credisClient->setOption($key, $value);
        }
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getChannelName($name)
    {
        if ($this->config['channel_prefix'] === null) {
            return $name;
        }

        return $this->config['channel_prefix'] . '_' . $name;
    }

    /**
     * @return string
     */
    public function getChannelPrefix()
    {
        if ($this->config['channel_prefix'] === null) {
            return null;
        }

        return $this->config['channel_prefix'] . '_';
    }

    /**
     * @param  mixed $value
     * @return string
     */
    public function serialize($value)
    {
        switch ($this->config['lib_options'][Redis::OPT_SERIALIZER]) {
            case Redis::SERIALIZER_PHP:
                return serialize($value);

            case Redis::SERIALIZER_IGBINARY:
                return igbinary_serialize($value);

            default:
                throw new RuntimeException('Invalid Redis serializer configuration');
        }
    }

    /**
     * @param  string $str
     * @return mixed
     */
    public function unserialize($str)
    {
        switch ($this->config['lib_options'][Redis::OPT_SERIALIZER]) {
            case Redis::SERIALIZER_PHP:
                return unserialize($str);

            case Redis::SERIALIZER_IGBINARY:
                return igbinary_unserialize($str);

            default:
                throw new RuntimeException('Invalid Redis serializer configuration');
        }
    }

    /**
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->credisClient, $name), $args);
    }
}
