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
