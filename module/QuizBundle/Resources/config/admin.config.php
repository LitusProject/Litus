<?php

return array(
    'submenus' => array(
        'Apps' => array(
            'subtitle'    => array('Quiz'),
            'items'       => array(
                'quiz_admin_quiz' => array('title' => 'Quiz'),
            ),
            'controllers' => array(
                'quiz_admin_quiz',
                'quiz_admin_round',
                'quiz_admin_team',
                'quiz_admin_tiebreaker',
            ),
        ),
    ),
);
