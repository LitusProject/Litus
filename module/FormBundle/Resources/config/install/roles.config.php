<?php

return array(
    'guest' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'form_manage' => array(
                'index',
            ),
            'form_manage_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'form_view' => array(
                'doodle', 'downloadFile', 'edit', 'index', 'view', 'saveDoodle',
            ),
            'form_group' => array(
                'view',
            ),
        ),
    ),
);
