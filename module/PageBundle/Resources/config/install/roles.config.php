<?php

return array(
    'editor' => array(
        'system' => true,
        'parents' => array(),
        'actions' => array(
            'page_admin_page' => array(
                'add', 'delete', 'edit', 'manage', 'upload', 'uploadProgress'
            ),
        )
    ),
    'guest' => array(
        'system' => true,
        'parents' => array(),
        'actions' => array(
            'page_link' => array(
                'view'
            ),
            'page' => array(
                'file', 'view'
            ),
        )
    ),
);
