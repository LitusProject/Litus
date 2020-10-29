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
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;

if (file_exists(__DIR__ . '/../session.config.php')) {
    // TODO: Remove this branch once all deployments have been containerized
    $sessionConfig = include __DIR__ . '/../session.config.php';
} else {
    $sessionConfig = array(
        'cookie_domain' => $_ENV['LITUS_SESSION_COOKIE_DOMAIN'] ?? '',
        'cookie_secure' => $_ENV['LITUS_SESSION_COOKIE_SECURE'] ?? true,
    );
}

$redisConfig = include __DIR__ . '/redis.global.php';
$redisConfig = $redisConfig['redis'];

return array(
    'session_config' => array_merge(
        array(
            'cookie_secure'    => getenv('APPLICATION_ENV') != 'development',
            'php_save_handler' => 'redis',
            'save_path'        => RedisUri::build($redisConfig, 'tcp'),
        ),
        $sessionConfig
    ),
    'session_manager' => array(
        'validators' => array(
            HttpUserAgent::class,
            RemoteAddr::class,
        ),
    ),
    'session_storage' => array(
        'type' => SessionArrayStorage::class,
    ),
);
