<?php

return array(
    'routes' => array(
        'quiz_admin_quiz' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/quiz[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'quiz_admin_quiz',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_admin_round' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/quiz/:quizid/round[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'quizid' => '[0-9]+',
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'quiz_admin_round',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_admin_team' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/quiz/:quizid/team[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'quizid' => '[0-9]+',
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'quiz_admin_team',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_admin_tiebreaker' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/quiz/:quizid/tiebreaker[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'quizid' => '[0-9]+',
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'quiz_admin_tiebreaker',
                    'action'     => 'manage',
                ),
            ),
        ),
        'quiz_quiz' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/quiz/:quizid[/:action[/:roundid/:teamid]][/]',
                'constraints' => array(
                    'quizid'  => '[0-9]+',
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'roundid' => '[0-9]*',
                    'teamid'  => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'quiz_quiz',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'quiz_admin_quiz'       => 'QuizBundle\Controller\Admin\QuizController',
        'quiz_admin_round'      => 'QuizBundle\Controller\Admin\RoundController',
        'quiz_admin_team'       => 'QuizBundle\Controller\Admin\TeamController',
        'quiz_admin_tiebreaker' => 'QuizBundle\Controller\Admin\TiebreakerController',
        'quiz_quiz'             => 'QuizBundle\Controller\QuizController',
    ),
);
