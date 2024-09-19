<?php

return array(
    'onbundle' => array(
        'on_admin_slug' => array(
            'add', 'delete', 'edit', 'manage', 'search', 'clean', 'old', 'clearOld',
        ),
        'on_redirect' => array(
            'index',
        ),
    ),
);
