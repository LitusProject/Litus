<?php

return array(
    'submenus' => array(
        'Site' => array(
            'subtitle'    => array('Pages'),
            'items'       => array(
                'page_admin_page' => array('title' => 'Pages'),
            ),
            'controllers' => array(
                'page_admin_category',
                'page_admin_link',
            ),
        ),
    ),
);
