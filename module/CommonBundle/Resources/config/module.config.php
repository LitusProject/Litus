<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

$asseticConfig = include __DIR__ . '/../../../../config/assetic.config.php';

return array(
    'router' => array(
        'routes' => array(
            'common_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/common[/]',
                    'defaults' => array(
                        'controller' => 'common_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'all_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/all[/]',
                    'defaults' => array(
                        'controller' => 'all_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_admin_academic' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/academic[/:action[/:id][/:field/:string][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                        'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_academic',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_admin_academic_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/academic/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_academic',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'common_admin_person_typeahead' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/person/typeahead[/:string][/]',
                    'constraints' => array(
                        'string'       => '[%a-zA-Z0-9:.,_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_person',
                        'action'     => 'typeahead',
                    ),
                ),
            ),
            'common_admin_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'common_admin_cache' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/cache[/:action[/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_cache',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_admin_config' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/config[/:action[/key/:key]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'key'    => '[a-zA-Z][\.a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_config',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_admin_index' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin[/]',
                    'defaults' => array(
                        'controller' => 'common_admin_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_admin_location' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/location[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_location',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_admin_role' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/role[/:action[/name/:name[/:id]][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'name'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_role',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_admin_session' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/session/:action[/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_session',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_admin_unit' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/unit[/:action[/:id][/page/:page]][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                        'page'   => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_admin_unit',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_index' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language][/]',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'common_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_account' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/account[/:action[/code/:code][/image/:image][/return/:return]][/]',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[a-zA-Z0-9_-]*',
                        'code'     => '[a-zA-Z0-9_-]*',
                        'image'    => '[a-zA-Z0-9]*',
                        'return'   => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_account',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_session' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/session[/:action[/:id]][/]',
                    'constraints' => array(
                        'language' => '[a-z]{2}',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'common_session',
                        'action'     => 'manage',
                    ),
                ),
            ),
            'common_auth' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/auth[/:action[/identification/:identification[/hash/:hash]]][/]',
                    'constraints' => array(
                        'action'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identification' => '[mrsu][0-9]{7}',
                        'hash'           => '[a-zA-Z0-9_-]*',
                        'language'       => '[a-z]{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'common_auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'common_robots' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/robots.txt',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'common_robots',
                        'action'     => 'index',
                    ),
                ),
            ),
            'common_praesidium' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '[/:language]/praesidium[/:action[/:academicyear]][/]',
                    'constraints' => array(
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'academicyear' => '[0-9]{4}-[0-9]{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'common_praesidium',
                        'action'     => 'overview',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'CommonBundle\Component\I18n\TranslatorServiceFactory',

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

            'common_sessionstorage' => function($serviceManager) {
                return new Zend\Session\Container('Litus_Common');
            },
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
            'common_install'        => 'CommonBundle\Controller\Admin\InstallController',
            'all_install'           => 'CommonBundle\Controller\Admin\AllInstallController',

            'common_admin_academic' => 'CommonBundle\Controller\Admin\AcademicController',
            'common_admin_auth'     => 'CommonBundle\Controller\Admin\AuthController',
            'common_admin_config'   => 'CommonBundle\Controller\Admin\ConfigController',
            'common_admin_cache'    => 'CommonBundle\Controller\Admin\CacheController',
            'common_admin_index'    => 'CommonBundle\Controller\Admin\IndexController',
            'common_admin_location' => 'CommonBundle\Controller\Admin\LocationController',
            'common_admin_person'   => 'CommonBundle\Controller\Admin\PersonController',
            'common_admin_role'     => 'CommonBundle\Controller\Admin\RoleController',
            'common_admin_session'  => 'CommonBundle\Controller\Admin\SessionController',
            'common_admin_unit'     => 'CommonBundle\Controller\Admin\UnitController',

            'common_index'          => 'CommonBundle\Controller\IndexController',
            'common_account'        => 'CommonBundle\Controller\AccountController',
            'common_session'        => 'CommonBundle\Controller\SessionController',
            'common_auth'           => 'CommonBundle\Controller\AuthController',
            'common_robots'         => 'CommonBundle\Controller\RobotsController',
            'common_praesidium'     => 'CommonBundle\Controller\PraesidiumController',
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
                    'CommonBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'commonbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'assetic_configuration' => array(
        'buildOnRequest' => getenv('APPLICATION_ENV') == 'development',
        'debug' => false,
        'webPath' => __DIR__ . '/../../../../public/_assetic',
        'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer' => 'AsseticBundle\View\ViewHelperStrategy'
        ),
        'cacheEnabled' => true,
        'cachePath' => __DIR__ . '/../../../../data/cache',
        'basePath' => '/_assetic/',
        'controllers' => $asseticConfig['controllers'],
        'routes' => $asseticConfig['routes'],
        'modules' => array(
            'commonbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'common_jquery' => array(
                        'assets'  => array(
                            'common/js/jquery.min.js',
                            'common/js/bootstrap-fileupload.min.js',
                        ),
                    ),
                    'common_jqueryui' => array(
                        'assets'  => array(
                            'common/js/jquery-ui.min.js',
                        ),
                    ),
                    'common_jqueryui_css' => array(
                        'assets' => array(
                            'common/css/jquery-ui.min.css',
                        ),
                    ),
                    'common_jqueryui_datepicker' => array(
                        'assets' => array(
                            'common/js/jquery-ui-timepicker-addon.js',
                        ),
                    ),
                    'common_jqueryui_datepicker_css' => array(
                        'assets' => array(
                            'common/css/jquery-ui-timepicker-addon.css',
                        ),
                    ),
                    'common_jquery_table_sort' => array(
                        'assets' => array(
                            'common/js/jquery.sortable-table.js'
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
                    'common_fieldcount' => array(
                        'assets'  => array(
                            'common/js/fieldcount.js',
                        ),
                    ),
                    'common_remote_typeahead' => array(
                        'assets'  => array(
                            'common/js/typeaheadRemote.js',
                        ),
                    ),
                    'common_spin_js' => array(
                        'assets'  => array(
                            'common/js/spin.min.js',
                        ),
                    ),
                    'common_holder_js' => array(
                        'assets'  => array(
                            'common/js/holder.js',
                        ),
                    ),
                    'common_chart_js' => array(
                        'assets'  => array(
                            'common/js/chart.min.js',
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

                    'bootstrap_js_rowlink' => array(
                        'assets'  => array(
                            'common/js/bootstrap-rowlink.js',
                        ),
                    ),
                    'bootstrap_js_custom_collapse' => array(
                        'assets'  => array(
                            'common/js/bootstrap-custom-collapse.js',
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
