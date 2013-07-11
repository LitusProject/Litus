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

return array(
    'router' => array(
        'routes' => array(
            'quiz_install' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/install/quiz[/]',
                    'constraints' => array(
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_install',
                        'action'     => 'index',
                    ),
                ),
            ),
            'quiz_admin_quiz' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/quiz[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_admin_quiz',
                        'action'     => 'manage',
                    ),
                ),
            ),
            // Alias: /admin/quiz/:id -> /admin/quiz/edit/:id
            array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/quiz/:id[/]',
                    'constraints' => array(
                        'id'      => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_admin_quiz',
                        'action'     => 'edit',
                    ),
                ),
            ),
            'quiz_admin_round' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/quiz/:quizid/round[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'quizid'  => '[0-9]+',
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_admin_round',
                        'action' => 'manage',
                    ),
                ),
            ),
            'quiz_admin_team' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/quiz/:quizid/team[/:action[/:id]][/page/:page][/]',
                    'constraints' => array(
                        'quizid'  => '[0-9]+',
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'      => '[0-9]*',
                        'page'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_admin_team',
                        'action' => 'manage',
                    ),
                ),
            ),
            'quiz_admin_moderate' => array(
                'type'    => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin/quiz/:quizid/moderate[/:action[/:roundid/:teamid]][/]',
                    'constraints' => array(
                        'quizid'  => '[0-9]+',
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'roundid'      => '[0-9]*',
                        'teamid'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'quiz_admin_moderate',
                        'action' => 'manage',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'quiz_layout' => __DIR__ . '/../layouts',
            'quiz_view' => __DIR__ . '/../views',
        ),
    ),
    'translator' => array(
        'translation_files' => array(
            /*array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.en.php',
                'locale'   => 'en'
            ),
            array(
                'type'     => 'phparray',
                'filename' => __DIR__ . '/../translations/site.nl.php',
                'locale'   => 'nl'
            ),*/
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'QuizBundle\Entity' => 'orm_annotation_driver'
                ),
            ),
            'orm_annotation_driver' => array(
                'paths' => array(
                    'quizbundle' => __DIR__ . '/../../Entity',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'quiz_install'              => 'QuizBundle\Controller\Admin\InstallController',
            'quiz_admin_quiz'           => 'QuizBundle\Controller\Admin\QuizController',
            'quiz_admin_round'          => 'QuizBundle\Controller\Admin\RoundController',
            'quiz_admin_team'           => 'QuizBundle\Controller\Admin\TeamController',
            'quiz_admin_moderate'       => 'QuizBundle\Controller\Admin\ModerateController',
        ),
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'quizbundle' => array(
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'moderate_css' => array(
                        'assets' => array(
                            'moderate/less/base.less',
                        ),
                        'filters' => array(
                            'moderate_less' => array(
                                'name' => 'Assetic\Filter\LessFilter',
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
                            'output' => 'moderate_css.css',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
