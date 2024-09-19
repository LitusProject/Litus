<?php

return array(
    'submenus' => array(
        'Secretary' => array(
            'subtitle'    => array('Promotions', 'Registrations', 'Pull'),
            'items'       => array(
                'secretary_admin_promotion'     => array('title' => 'Promotions'),
                'secretary_admin_registration'  => array('title' => 'Registrations'),
                'secretary_admin_working_group' => array('title' => 'Working Group Members', 'action' => 'manage'),
                'secretary_admin_pull'          => array('title' => 'Departmental Pull', 'action' => 'manage'),
            ),
            'controllers' => array(
                'secretary_admin_export',
            ),
        ),
    ),
);
