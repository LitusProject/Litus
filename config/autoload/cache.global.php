<?php

$redisConfig = include __DIR__ . '/redis.global.php';
$redisConfig = $redisConfig['redis'];

return array(
    'cache' => array(
        'storage' => array(
            'adapter' => array(
                'name'    => 'redis',
                'options' => array(
                    'ttl'           => 0,
                    'namespace'     => 'cache:litus',

                    'database'      => $redisConfig['database'],
                    'lib_options'   => $redisConfig['lib_options'],
                    'password'      => $redisConfig['password'],
                    'persistent_id' => $redisConfig['persistent_id'],
                    'server'        => array(
                        'host'    => $redisConfig['host'],
                        'port'    => $redisConfig['port'],
                        'timeout' => $redisConfig['timeout'],
                    ),
                ),
            ),
        ),
    ),
);
