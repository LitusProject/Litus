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

if ('development' != getenv('APPLICATION_ENV')) {
    if (!file_exists(__DIR__ . '/../sentry.config.php')) {
        throw new RuntimeException(
            'The Sentry configuration file (' . (__DIR__ . '/../sentry.config.php') . ') was not found'
        );
    }

    return array(
        'service_manager' => array(
            'factories' => array(
                'sentry' => function ($serviceManager) {
                    return new \CommonBundle\Component\Sentry\Client(
                        $serviceManager->get('raven_client'),
                        $serviceManager->get('authentication')
                    );
                },
                'raven_client' => function ($serviceManager) {
                    $sentryConfig = include __DIR__ . '/../sentry.config.php';

                    return new \Raven_Client(
                        $sentryConfig['dsn'],
                        $sentryConfig['options']
                    );
                },
            ),
        ),
    );
}

return array();
