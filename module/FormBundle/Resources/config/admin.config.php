<?php

return array(
    'submenus' => array(
        'Site' => array(
            'items' => array(
                'form_admin_form' => array('title' => 'Forms'),
            ),
            'controllers' => array(
                'form_admin_form_field',
                'form_admin_viewer',
            ),
        ),
    ),
);
