<?php

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
        ),
    ),
    'session_storage' => array(
        'type' => SessionArrayStorage::class,
    ),
);
