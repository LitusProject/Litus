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

return array(
    'routes' => array(
        'quiz_admin_quiz' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/quiz[/:action[/:id][/page/:page]][/]',
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
        'quiz_admin_round' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/quiz/:quizid/round[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'quizid'  => '[0-9]+',
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'quiz_admin_round',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_admin_team' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/admin/quiz/:quizid/team[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'quizid'  => '[0-9]+',
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'page'    => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'quiz_admin_team',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_quiz' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/quiz/:quizid[/:action[/:roundid/:teamid]][/]',
                'constraints' => array(
                    'quizid'  => '[0-9]+',
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'roundid' => '[0-9]*',
                    'teamid'  => '[0-9]*',
                ),
                'defaults' => array(
                    'controller' => 'quiz_quiz',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'quiz_admin_quiz'           => 'QuizBundle\Controller\Admin\QuizController',
        'quiz_admin_round'          => 'QuizBundle\Controller\Admin\RoundController',
        'quiz_admin_team'           => 'QuizBundle\Controller\Admin\TeamController',
        'quiz_quiz'                 => 'QuizBundle\Controller\QuizController',
    ),
);
