<?php

use Doctrine\DBAL\Driver\PDOPgSql\Driver as ORMDefaultDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as ORMAnnotationDriver;

if (file_exists(__DIR__ . '/../database.config.php')) {
    // TODO: Remove this branch once all deployments have been containerized
    $databaseConfig = include __DIR__ . '/../database.config.php';
    $databaseConfig = $databaseConfig['relational'];
} else {
    $password = $_ENV['LITUS_DATABASE_PASSWORD'] ?? '';
    if (isset($_ENV['LITUS_DATABASE_PASSWORD_FILE'])) {
        $password = file_get_contents($_ENV['LITUS_DATABASE_PASSWORD_FILE']);
    }

    $databaseConfig = array(
        'host'     => $_ENV['LITUS_DATABASE_HOST'] ?? 'localhost',
        'port'     => $_ENV['LITUS_DATABASE_PORT'] ?? 5432,
        'user'     => $_ENV['LITUS_DATABASE_USER'] ?? 'litus',
        'password' => $password,
        'dbname'   => $_ENV['LITUS_DATABASE_DBNAME'] ?? 'litus',
    );
}

return array(
    'doctrine' => array(
        'cache' => array(
            'redis' => array(
                'namespace' => 'cache:doctrine',
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'metadata_cache'   => 'redis',
                'query_cache'      => 'redis',
                'result_cache'     => 'redis',
                'hydration_cache'  => 'redis',
                'generate_proxies' => getenv('APPLICATION_ENV') == 'development',
                'proxy_dir'        => 'data/proxies/',
            ),
        ),
        'connection' => array(
            'orm_default' => array(
                'driverClass' => ORMDefaultDriver::class,
                'params'      => $databaseConfig,
            ),
        ),
        'driver' => array(
            'orm_annotation_driver' => array(
                'class' => ORMAnnotationDriver::class,
            ),
        ),

        'migrations_configuration' => array(
            'orm_default' => array(
                'directory'       => __DIR__ . '/../../migrations',
                'namespace'       => 'Migrations',
                'table'           => 'general_migrations',
                'custom_template' => __DIR__ . '/../../migrations/migration.tpl',
            ),
        ),
    ),
);
