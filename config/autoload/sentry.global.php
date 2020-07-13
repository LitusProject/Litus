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
