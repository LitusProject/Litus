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

namespace CommonBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'countries'),
        'has_layouts'       => true,
    ),
    array(
        'service_manager' => array(
            'factories' => array(
                'authentication' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Authentication(
                        $serviceManager->get('authentication_credentialadapter'),
                        $serviceManager->get('authentication_doctrineservice')
                    );
                },
                'authentication_credentialadapter' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Adapter\Doctrine\Credential(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Person',
                        'username'
                    );
                },
                'authentication_doctrineservice' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Service\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Session',
                        2678400,
                        $serviceManager->get('authentication_sessionstorage'),
                        'Litus_Auth',
                        'Session',
                        $serviceManager->get('authentication_action')
                    );
                },
                'authentication_action' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Action\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        $serviceManager->get('mail_transport')
                    );
                },
                'authentication_sessionstorage' => function ($serviceManager) {
                    return new \Zend\Authentication\Storage\Session('Litus_Auth');
                },

                'common_sessionstorage' => function ($serviceManager) {
                    return new \Zend\Session\Container('Litus_Common');
                },

                'AsseticBundle\Service' => 'CommonBundle\Component\Assetic\ServiceFactory',

                'doctrine.cli' => 'CommonBundle\Component\Console\ApplicationFactory',
                'litus.console_router' => 'CommonBundle\Component\Mvc\Router\Console\RouteStackFactory',
            ),
            'invokables' => array(
                'mail_transport' => 'Zend\Mail\Transport\Sendmail',
                'AsseticCacheBuster' => 'AsseticBundle\CacheBuster\LastModifiedStrategy',
            ),
            'aliases' => array(
                'litus.console_application' => 'doctrine.cli',
                'translator' => 'MvcTranslator',
            ),
        ),
        'translator' => array(
            'translation_file_patterns' => array(
                array(
                    'type'     => 'phparray',
                    'base_dir' => './vendor/zendframework/zendframework/resources/languages',
                    'pattern'  => '%s/Zend_Validate.php',
                ),
            ),
        ),
        'view_manager' => array(
            'template_map' => array(
                'layout/layout' => __DIR__ . '/../layouts/layout.twig',
                'error/404'     => __DIR__ . '/../views/error/404.twig',
                'error/index'   => __DIR__ . '/../views/error/index.twig',
            ),
            'doctype' => 'HTML5',

            'not_found_template' => 'error/404',
            'exception_template' => 'error/index',

            'display_not_found_reason' => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
            'display_exceptions'       => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
        ),
        'assetic_configuration' => array(
            'buildOnRequest' => getenv('APPLICATION_ENV') == 'development',
            'debug' => false,
            'webPath' => __DIR__ . '/../../../../public/_assetic',
            'cacheEnabled' => true,
            'cachePath' => __DIR__ . '/../../../../data/cache',
            'basePath' => '/_assetic/',
        ),
        'assetic_filters' => array(
            'invokables' => array(
                'css'  => 'CommonBundle\Component\Assetic\Filter\Css',
                'js'   => 'CommonBundle\Component\Assetic\Filter\Js',
                'less' => 'CommonBundle\Component\Assetic\Filter\Less',
            ),
        ),
        'authentication_sessionstorage' => array(
            'namespace' => 'Litus_Auth',
            'member'    => 'storage',
        ),
    )
);
