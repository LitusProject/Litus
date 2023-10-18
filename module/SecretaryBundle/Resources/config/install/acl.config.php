<?php

return array(
    'secretarybundle' => array(
        'secretary_registration' => array(
            'add', 'complete', 'edit', 'saveStudies', 'saveSubjects', 'studies', 'subjects', 'preferences', 'savePreferences'
        ),
        'secretary_admin_registration' => array(
            'add', 'barcode', 'cancel', 'edit', 'manage', 'reprint', 'search',
        ),
        'secretary_admin_export' => array(
            'download', 'export',
        ),
        'secretary_admin_photos' => array(
            'download', 'photos',
        ),
        'secretary_admin_promotion' => array(
            'add', 'delete', 'manage', 'search', 'update',
        ),
        'secretary_admin_working_group' => array(
            'manage', 'delete',
        ),
        'secretary_admin_pull' => array(
            'manage', 'add', 'delete', 'edit',
        ),
        'secretary_pull' => array(
            'pay', 'payed', 'view',
        ),
    ),
);
