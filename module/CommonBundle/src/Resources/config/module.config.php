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

$asseticConfig = include __DIR__ . '/../../../../../config/assetic.config.php';

return array(
    'router' => array(
        'routes' => array(
            'index' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'account' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/account[/:action[/code/:code]]',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[a-zA-Z0-9_-]*',
                        'code'     => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'account',
                        'action'     => 'index',
                    ),
                ),
            ),
            'auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/auth[/:action[/identification/:identification[/hash/:hash]]]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                        'language'       => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'common_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/common',
                    'defaults' => array(
                        'controller' => 'common_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'all_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/all',
                    'defaults' => array(
                        'controller' => 'all_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_academic' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/academic[/:action[/:id][/page/:page][/:field/:string]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[a-zA-Z][%a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_academic',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_academic_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/academic/typeahead[/:string]',
                    'constraints' => array(
                        'string'       => '[%a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_academic',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'admin_person_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/person/typeahead[/:string]',
                    'constraints' => array(
                        'string'       => '[%a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_person',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'admin_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/auth[/:action[/identification/:identification[/hash/:hash]]]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'admin_cache' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/cache/:action',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_cache',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_config' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/config[/:action[/key/:key]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'key'    => '[a-zA-Z][\.a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_config',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_index' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => 'admin_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_location' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/location[/:action[/:id][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_location',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'admin_role' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/role[/:action[/name/:name][/page/:page]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'name'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'admin_role',
                        'action'     => 'manage',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'CommonBundle\Component\I18n\TranslatorServiceFactory',
            'authentication' => function ($serviceManager) {
                $authentication = new \CommonBundle\Component\Authentication\Authentication(
                    $serviceManager->get('authentication_credentialadapter'),
                    $serviceManager->get('authentication_doctrineservice')
                );
                return $authentication;
            },
            'authentication_credentialadapter' => function ($serviceManager) {
                $adapter = new \CommonBundle\Component\Authentication\Adapter\Doctrine\Credential(
                    $serviceManager->get('doctrine.entitymanager.orm_default'),
                    'CommonBundle\Entity\Users\Person',
                    'username'
                );
                return $adapter;
            },
            'authentication_doctrineservice' => function ($serviceManager) {
                $service = new \CommonBundle\Component\Authentication\Service\Doctrine(
                    $serviceManager->get('doctrine.entitymanager.orm_default'),
                    'CommonBundle\Entity\Users\Session',
                    2678400,
                    $serviceManager->get('authentication_sessionstorage'),
                    'Litus_Auth',
                    'Session',
                    'authentication_action'
                );
                return $service;
            },
            'authentication_action' => function ($serviceManager) {
                $authentication = new \CommonBundle\Component\Authentication\Action\Doctrine(
                    $serviceManager->get('doctrine.entitymanager.orm_default'),
                    $serviceManager->get('mail_transport')
                );
                return $authentication;
            },
            'authentication_sessionstorage' => function ($serviceManager) {
                $storage = new \Zend\Authentication\Storage\Session(
                    'Litus_Auth'
                );
                return $storage;
            }
        ),
        'invokables' => array(
            'mail_transport' => 'Zend\Mail\Transport\Sendmail',
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/countries.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/countries.nl.php',
                'locale'   => 'nl'
            ),
            array(
                'type'     => 'phparray',
                'filename' => './vendor/zendframework/zendframework/resources/languages/en/Zend_Validate.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => './vendor/zendframework/zendframework/resources/languages/nl/Zend_Validate.php',
                'locale'   => 'nl'
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'common_install' => 'CommonBundle\Controller\Admin\InstallController',
            'all_install'    => 'CommonBundle\Controller\Admin\AllInstallController',

            'index'          => 'CommonBundle\Controller\IndexController',
            'account'        => 'CommonBundle\Controller\AccountController',
            'auth'           => 'CommonBundle\Controller\AuthController',
            'admin_academic' => 'CommonBundle\Controller\Admin\AcademicController',
            'admin_auth'     => 'CommonBundle\Controller\Admin\AuthController',
            'admin_config'   => 'CommonBundle\Controller\Admin\ConfigController',
            'admin_cache'    => 'CommonBundle\Controller\Admin\CacheController',
            'admin_index'    => 'CommonBundle\Controller\Admin\IndexController',
            'admin_location' => 'CommonBundle\Controller\Admin\LocationController',
            'admin_person'   => 'CommonBundle\Controller\Admin\PersonController',
            'admin_role'     => 'CommonBundle\Controller\Admin\RoleController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../layouts/layout.twig',
            'error/404'     => __DIR__ . '/../views/error/404.twig',
            'error/index'   => __DIR__ . '/../views/error/index.twig',
        ),
        'template_path_stack' => array(
            'commonbundle_layout' => __DIR__ . '/../layouts',
            'commonbundle_view'   => __DIR__ . '/../views',
        ),

        'doctype' => 'HTML5',

        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',

        'display_not_found_reason' => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
        'display_exceptions'       => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'CommonBundle\Entity' => 'my_annotation_driver'
                ),
            ),
            'my_annotation_driver' => array(
                'paths' => array(
                    'commonbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'assetic_configuration' => array(
        'debug' => false,
        'webPath' => './public/_assetic',
        'strategyForRenderer' => array(
            'AsseticBundle\View\ViewHelperStrategy' => 'Zend\View\Renderer\PhpRenderer'
        ),
        'cacheEnabled' => true,
        'cachePath' => './data/cache',
        'baseUrl' => '/_assetic',
        'controllers' => $asseticConfig['controllers'],
        'routes' => $asseticConfig['routes'],
        'modules' => array(
            'commonbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'common_jquery' => array(
                        'assets'  => array(
                            'common/js/jquery-1.7.2.min.js',
                        ),
                    ),
                    'common_jqueryui' => array(
                        'assets'  => array(
                            'common/js/jqueryui-1.8.16.min.js',
                        ),
                    ),
                    'common_jquery_form' => array(
                        'assets'  => array(
                            'common/js/jquery.form.js',
                        ),
                    ),
                    'common_form_upload_progress' => array(
                        'assets'  => array(
                            'common/js/formUploadProgress.js',
                        ),
                    ),
                    'common_permanent_modal' => array(
                        'assets'  => array(
                            'common/js/permanentModal.js',
                        ),
                    ),
                    'common_socket' => array(
                        'assets'  => array(
                            'common/js/socket.js',
                        ),
                    ),
                    'common_download_file' => array(
                        'assets'  => array(
                            'common/js/downloadFile.js',
                        ),
                    ),
                    'common_typeahead_remote' => array(
                        'assets'  => array(
                            'common/js/typeaheadRemote.js',
                        ),
                    ),
                    'common_spin_js' => array(
                        'assets'  => array(
                            'common/js/spin.min.js',
                        ),
                    ),

                    'admin_css' => array(
                        'assets' => array(
                            'admin/less/admin.less',
                        ),
                        'filters' => array(
                            'admin_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'admin_css.css',
                        ),
                    ),
                    'admin_js' => array(
                        'assets'  => array(
                            'admin/js/*.js',
                        ),
                    ),

                    'site_css' => array(
                        'assets' => array(
                            'site/less/base.less',
                        ),
                        'filters' => array(
                            'site_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'site_css.css'
                        ),
                    ),

                    'bootstrap_css' => array(
                        'assets' => array(
                            'bootstrap/less/bootstrap.less',
                        ),
                        'filters' => array(
                            'bootstrap_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_css.css',
                        ),
                    ),
                    'bootstrap_responsive_css' => array(
                        'assets' => array(
                            'bootstrap/less/responsive.less',
                        ),
                        'filters' => array(
                            'bootstrap_responsive_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => false,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_responsive_css.css',
                        ),
                    ),
                    'bootstrap_js_alert' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-alert.js',
                        ),
                    ),
                    'bootstrap_js_button' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-button.js',
                        ),
                    ),
                    'bootstrap_js_carousel' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-carousel.js',
                        ),
                    ),
                    'bootstrap_js_collapse' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-collapse.js',
                        ),
                    ),
                    'bootstrap_js_dropdown' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-dropdown.js',
                        ),
                    ),
                    'bootstrap_js_modal' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-modal.js',
                        ),
                    ),
                    'bootstrap_js_popover' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-popover.js',
                        ),
                    ),
                    'bootstrap_js_scrollspy' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-scrollspy.js',
                        ),
                    ),
                    'bootstrap_js_tab' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-tab.js',
                        ),
                    ),
                    'bootstrap_js_tooltip' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-tooltip.js',
                        ),
                    ),
                    'bootstrap_js_transition' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-transition.js',
                        ),
                    ),
                    'bootstrap_js_typeahead' => array(
                        'assets' => array(
                            'bootstrap/js/bootstrap-typeahead.js',
                        ),
                    ),

                    'gollum_css' => array(
                        'assets' => array(
                            'gollum/css/editor.css'
                        ),
                    ),
                    'gollum_js' => array(
                        'assets' => array(
                            'gollum/js/editor.js',
                            'gollum/js/markdown.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'authentication_sessionstorage' => array(
        'namespace' => 'Litus_Auth',
        'member'    => 'storage',
    ),
);