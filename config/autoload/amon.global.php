<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

if ('production' == getenv('APPLICATION_ENV')) {
    if (!file_exists(__DIR__ . '/../amon.config.php')) {
        throw new RuntimeException(
            'The Amon configuration file (' . (__DIR__ . '/../amon.config.php') . ') was not found'
        );
    }

    return array(
        'service_manager' => array(
            'factories' => array(
                'amon' => function ($serviceManager) {
                    $client = new \CommonBundle\Component\Amon\Client(
                        $serviceManager->get('amon_connection')
                    );
                    return $client;
                },
                'amon_connection' => function ($serviceManager) {
                    $amonConfig = include __DIR__ . '/../amon.config.php';

                    switch ($amonConfig['protocol']) {
                        case 'http':
                            $connection = new \CommonBundle\Component\Amon\Connection\Http(
                                $amonConfig['server'], $amonConfig['port'], $amonConfig['secretKey']
                            );
                        break;
                        case 'zeromq':
                            $connection = null;
                    }

                    return $connection;
                }
            ),
        ),
    );
} else {
    return array();
}