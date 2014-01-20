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
 *
 * @license http://litus.cc/LICENSE
 */

if ('development' != getenv('APPLICATION_ENV')) {
    if (!file_exists(__DIR__ . '/../lilo.config.php')) {
        throw new RuntimeException(
            'The Lilo configuration file (' . (__DIR__ . '/../lilo.config.php') . ') was not found'
        );
    }

    return array(
        'service_manager' => array(
            'factories' => array(
                'lilo' => function ($serviceManager) {
                    return new \CommonBundle\Component\Lilo\Client(
                        $serviceManager->get('lilo_connection'),
                        $serviceManager->get('authentication')
                    );
                },
                'lilo_connection' => function ($serviceManager) {
                    $liloConfig = include __DIR__ . '/../lilo.config.php';

                    return new \CommonBundle\Component\Lilo\Connection\Http(
                        $liloConfig['host'], $liloConfig['secure'], $liloConfig['secretKey']
                    );
                }
            ),
        ),
    );
}

return array();
