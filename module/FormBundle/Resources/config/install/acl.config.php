<?php

return array(
    'formbundle' => array(
        'form_admin_form' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'form_admin_group' => array(
            'add', 'delete', 'deleteForm', 'edit', 'forms', 'manage', 'old', 'sort'
        ),
        'form_admin_form_field' => array(
            'add', 'delete', 'edit', 'manage', 'sort'
        ),
        'form_admin_form_viewer' => array(
            'add', 'delete', 'manage'
        ),
        'form_admin_group_viewer' => array(
            'add', 'delete', 'manage'
        ),
        'form_view' => array(
            'doodle', 'downloadFile', 'edit', 'view', 'saveDoodle'
        ),
        'form_group' => array(
            'view',
        ),
        'form_manage' => array(
            'delete', 'doodle', 'download', 'downloadFile', 'edit', 'index', 'view',
        ),
        'form_manage_group' => array(
            'index', 'view',
        ),
        'form_manage_mail' => array(
            'send'
        ),
        'form_manage_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
    ),
);
