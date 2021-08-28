<?php

if (file_exists(__DIR__ . '/../redis.config.php')) {
    // TODO: Remove this branch once all deployments have been containerized
    $redisConfig = include __DIR__ . '/../redis.config.php';
} else {
    $password = $_ENV['LITUS_REDIS_PASSWORD'] ?? '';
    if (isset($_ENV['LITUS_REDIS_PASSWORD_FILE'])) {
        $password = file_get_contents($_ENV['LITUS_REDIS_PASSWORD_FILE']);
    }

    $serializer = Redis::SERIALIZER_PHP;
    if (extension_loaded('igbinary')) {
        $serializer = Redis::SERIALIZER_IGBINARY;
    }

    $libOptions = array(
        Redis::OPT_SERIALIZER => $serializer,
    );

    $redisConfig = array(
        'host'           => $_ENV['LITUS_REDIS_HOST'] ?? 'localhost',
        'port'           => $_ENV['LITUS_REDIS_PORT'] ?? 6379,
        'timeout'        => $_ENV['LITUS_REDIS_TIMEOUT'] ?? null,
        'persistent_id'  => $_ENV['LITUS_REDIS_PERSISTENT_ID'] ?? '',
        'password'       => $password,
        'database'       => $_ENV['LITUS_REDIS_DATABASE'] ?? 0,
        'channel_prefix' => $_ENV['LITUS_REDIS_CHANNEL_PREFIX'] ?? null,
        'lib_options'    => $libOptions,
    );
}

return array(
    'redis' => $redisConfig,
);
