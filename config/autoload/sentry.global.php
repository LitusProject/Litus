<?php

use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use CommonBundle\Component\Version\Version;

if (getenv('APPLICATION_ENV') != 'development') {
    if (file_exists(__DIR__ . '/../sentry.config.php')) {
        // TODO: Remove this branch once all deployments have been containerized
        $sentryConfig = include __DIR__ . '/../sentry.config.php';
    } else {
        $sentryConfig = array(
            'dsn'     => $_ENV['LITUS_SENTRY_DSN'] ?? '',
            'options' => array(
                'name'     => $_ENV['LITUS_SENTRY_NAME'] ?? gethostname(),
                'app_path' => $_ENV['LITUS_SENTRY_APP_PATH'] ?? '',
            ),
        );
    }

    return array(
        'sentry' => array(
            'dsn'     => $sentryConfig['dsn'],
            'options' => array_merge(
                array(
                    'name'                => gethostname(),
                    'release'             => Version::getCommitHash(),
                    'excluded_exceptions' => array(
                        HasNoAccessException::class,
                    ),
                ),
                $sentryConfig['options']
            ),
        ),
    );
}

return array();
