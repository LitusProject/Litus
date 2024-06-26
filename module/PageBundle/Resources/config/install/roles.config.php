<?php

return array(
    'editor' => array(
        'system'  => true,
        'parents' => array(),
        'actions' => array(
            'page_admin_page' => array(
                'add', 'delete', 'edit', 'manage', 'upload',
            ),
            'page_admin_categorypage' => array(
                'add', 'delete', 'edit', 'manage',
            ),
            'page_admin_categorypage_frame' => array(
                'add', 'delete', 'edit', 'manage', 'editPoster', 'upload',
            ),
        ),
    ),
    'guest' => array(
        'system'  => true,
        'parents' => array(),
        'actions' => array(
            'page_link' => array(
                'view',
            ),
            'page' => array(
                'file', 'view',
            ),
            'page_categorypage' => array(
                'view', 'poster',
            ),
        ),
    ),
);
