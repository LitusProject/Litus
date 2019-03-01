<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Redis;

use Credis_Client;
use Redis;
use RuntimeException;

/**
 * Redis Client
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Client
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var Credis_Client
     */
    private $credisClient;

    /**
     * @param  Configuration $config
     * @return self
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;

        $this->credisClient = new Credis_Client(
            $config->getHost(),
            $config->getPort(),
            $config->getTimeout(),
            $config->getPersistentId(),
            $config->getDatabase(),
            $config->getPassword()
        );

        foreach ($config->getLibOptions() as $key => $value) {
            $this->credisClient->setOption($key, $value);
        }
    }

    /**
     * @param  mixed $value
     * @return string
     */
    public function serialize($value)
    {
        switch ($this->config->getLibOptions()[Redis::OPT_SERIALIZER]) {
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
        switch ($this->config->getLibOptions()[Redis::OPT_SERIALIZER]) {
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
