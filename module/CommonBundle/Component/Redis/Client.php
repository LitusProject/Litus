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

class Client
{
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
     * @param string $name
     * @param
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->credisClient, $name), $args);
    }
}
