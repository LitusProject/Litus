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

use CommonBundle\Component\Redis\Uri as RedisUri;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;

if (!file_exists(__DIR__ . '/../session.config.php')) {
    throw new RuntimeException(
        'The session configuration file (' . (__DIR__ . '/../session.config.php') . ') was not found'
    );
}

$sessionConfig = include __DIR__ . '/../session.config.php';

if (getenv('APPLICATION_ENV') != 'development') {
    if (!extension_loaded('redis')) {
        throw new RuntimeException('Litus requires the Redis extension to be loaded');
    }

    if (!file_exists(__DIR__ . '/../redis.config.php')) {
        throw new RuntimeException(
            'The Redis configuration file (' . (__DIR__ . '/../redis.config.php') . ') was not found'
        );
    }

    $redisConfig = include __DIR__ . '/../redis.config.php';

    return array(
        'session_config' => array_merge(
            array(
                'cookie_secure'    => true,
                'php_save_handler' => 'redis',
                'save_path'        => RedisUri::build($redisConfig, 'tcp'),
            ),
            $sessionConfig
        ),
        'session_manager' => array(
            'validators' => array(
                RemoteAddr::class,
                HttpUserAgent::class,
            )
        ),
        'session_storage' => array(
            'type' => SessionArrayStorage::class,
        ),
    );
}

return array(
    'session_config'  => $sessionConfig,
    'session_manager' => array(
        'validators' => array(
            RemoteAddr::class,
            HttpUserAgent::class,
        )
    ),
    'session_storage' => array(
        'type' => SessionArrayStorage::class,
    ),
);
