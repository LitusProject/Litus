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
                    return new Component\Authentication\Authentication(
                        $serviceManager->get('authentication_credentialadapter'),
                        $serviceManager->get('authentication_service')
                    );
                },
                'authentication_doctrinecredentialadapter' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Adapter\Doctrine\Credential(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Person',
                        'username'
                    );
                },
                'authentication_doctrineservice' => function ($serviceManager) {
                    return new Component\Authentication\Service\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Session',
                        2678400,
                        $serviceManager->get('authentication_sessionstorage'),
                        'Litus_Auth',
                        'Session',
                        $serviceManager->get('authentication_action')
                    );
                },
                'authentication_doctrineaction' => function ($serviceManager) {
                    return new \CommonBundle\Component\Authentication\Action\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        $serviceManager->get('mail_transport')
                    );
                },
                'authentication_sessionstorage' => function () {
                    return new \Zend\Authentication\Storage\Session(getenv('ORGANIZATION') . '_Litus_Auth');
                },

                'common_sessionstorage' => function () {
                    return new \Zend\Session\Container(getenv('ORGANIZATION') . '_Litus_Common');
                },

                'AsseticBundle\Service' => 'CommonBundle\Component\Assetic\ServiceFactory',

                'doctrine.cli' => 'CommonBundle\Component\Console\ApplicationFactory',
                'litus.console_router' => 'CommonBundle\Component\Mvc\Router\Console\RouteStackFactory',

                'formfactory.bootstrap' => new Component\Form\FactoryFactory(false),
                'formfactory.admin'     => new Component\Form\FactoryFactory(true),
            ),
            'invokables' => array(
                'mail_transport'     => 'Zend\Mail\Transport\Sendmail',
                'AsseticCacheBuster' => 'AsseticBundle\CacheBuster\LastModifiedStrategy',

                'litus.hydratormanager'    => 'CommonBundle\Component\Hydrator\HydratorPluginManager',
            ),
            'aliases' => array(
                'litus.console_application' => 'doctrine.cli',

                'authentication_service' => 'authentication_doctrineservice',
                'authentication_credentialadapter' => 'authentication_doctrinecredentialadapter',
                'authentication_action' => 'authentication_doctrineaction',

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
        'view_helpers' => array(
            'invokables' => array(
                'url'           => 'CommonBundle\Component\View\Helper\Url',
                'hasaccess'     => 'CommonBundle\Component\View\Helper\HasAccess',
                'getparam'      => 'CommonBundle\Component\View\Helper\GetParam',
                'datelocalized' => 'CommonBundle\Component\View\Helper\DateLocalized',
                'staticmap'     => 'CommonBundle\Component\View\Helper\StaticMap',
                'hideemail'     => 'CommonBundle\Component\View\Helper\HideEmail',
            ),
        ),
        'controller_plugins' => array(
            'invokables' => array(
                'url'            => 'CommonBundle\Component\Controller\Plugin\Url',
                'hasaccess'      => 'CommonBundle\Component\Controller\Plugin\HasAccess',
                'paginator'      => 'CommonBundle\Component\Controller\Plugin\Paginator',
                'flashmessenger' => 'CommonBundle\Component\Controller\Plugin\FlashMessenger',
            ),
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
            'namespace' => getenv('ORGANIZATION') . '_Litus_Auth',
            'member'    => 'storage',
        ),
        'litus' => array(
            'forms' => array(
                'bootstrap' => array(
                    'invokables' => array(
                        'button'     => 'CommonBundle\Component\Form\Bootstrap\Element\Button',
                        'checkbox'   => 'CommonBundle\Component\Form\Bootstrap\Element\Checkbox',
                        'collection' => 'CommonBundle\Component\Form\Collection',
                        'file'       => 'CommonBundle\Component\Form\Bootstrap\Element\File',
                        'hidden'     => 'CommonBundle\Component\Form\Bootstrap\Element\Hidden',
                        'password'   => 'CommonBundle\Component\Form\Bootstrap\Element\Password',
                        'radio'      => 'CommonBundle\Component\Form\Bootstrap\Element\Radio',
                        'reset'      => 'CommonBundle\Component\Form\Bootstrap\Element\Reset',
                        'select'     => 'CommonBundle\Component\Form\Bootstrap\Element\Select',
                        'submit'     => 'CommonBundle\Component\Form\Bootstrap\Element\Submit',
                        'text'       => 'CommonBundle\Component\Form\Bootstrap\Element\Text',
                        'textarea'   => 'CommonBundle\Component\Form\Bootstrap\Element\Textarea',
                        'typeahead'  => 'CommonBundle\Component\Form\Bootstrap\Element\TypeAhead',

                        'fieldset'   => 'CommonBundle\Component\Form\Fieldset',
                    ),
                ),
                'admin'     => array(
                    'invokables' => array(
                        'checkbox'   => 'CommonBundle\Component\Form\Admin\Element\Checkbox',
                        'collection' => 'CommonBundle\Component\Form\Collection',
                        'csrf'       => 'CommonBundle\Component\Form\Admin\Element\Csrf',
                        'date'       => 'CommonBundle\Component\Form\Admin\Element\Date',
                        'datetime'   => 'CommonBundle\Component\Form\Admin\Element\DateTime',
                        'file'       => 'CommonBundle\Component\Form\Admin\Element\File',
                        'hidden'     => 'CommonBundle\Component\Form\Admin\Element\Hidden',
                        'password'   => 'CommonBundle\Component\Form\Admin\Element\Password',
                        'radio'      => 'CommonBundle\Component\Form\Admin\Element\Radio',
                        'select'     => 'CommonBundle\Component\Form\Admin\Element\Select',
                        'tabs'       => 'CommonBundle\Component\Form\Admin\Element\Tabs',
                        'text'       => 'CommonBundle\Component\Form\Admin\Element\Text',
                        'textarea'   => 'CommonBundle\Component\Form\Admin\Element\Textarea',
                        'time'       => 'CommonBundle\Component\Form\Admin\Element\Time',
                        'typeahead'  => 'CommonBundle\Component\Form\Admin\Element\TypeAhead',

                        'tabcontent' => 'CommonBundle\Component\Form\Admin\Form\SubForm\TabContent',
                        'tabpane'    => 'CommonBundle\Component\Form\Admin\Form\SubForm\TabPane',

                        'fieldset'   => 'CommonBundle\Component\Form\Fieldset',
                    ),
                ),
            ),
        ),
        'filters' => array(
            'invokables' => array(
                'stripcarriagereturn' => 'CommonBundle\Component\Filter\StripCarriageReturn',
            ),
        ),
    )
);
