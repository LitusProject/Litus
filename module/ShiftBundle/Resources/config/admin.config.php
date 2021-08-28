<?php

return array(
    'submenus' => array(
        'Shifts' => array(
            'subtitle' => array('Counter', 'Rankings', 'Shifts', 'Registration Shifts'),
            'items'    => array(
                'shift_admin_shift_counter' => array(
                    'action' => 'index',
                    'title'  => 'Counter',
                ),
                'shift_admin_shift_ranking' => array(
                    'action' => 'index',
                    'title'  => 'Ranking',
                ),
                'shift_admin_shift_weekly_change' => array(
                    'action' => 'index',
                    'title'  => 'Weekly Change',
                ),
                'shift_admin_shift' => array(
                    'action' => 'manage',
                    'title'  => 'Shifts',
                ),
                'shift_admin_registration_shift' => array(
                    'action' => 'manage',
                    'title'  => 'Registration Shifts',
                ),
            ),
            'controllers' => array('shift_admin_unit'),
        ),
    ),
);
