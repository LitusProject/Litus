<?php

return array(
    'guest' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_admin_auth' => array(
                'authenticate', 'login', 'logout', 'shibboleth',
            ),
            'common_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'common_contact' => array(
                'index',
            ),
            'common_index' => array(
                'index',
            ),
            'common_account' => array(
                'activate',
            ),
            'common_robots' => array(
                'index',
            ),
            'common_praesidium' => array(
                'overview',
            ),
            'common_poc' => array(
                'overview',
            ),
            'common_privacy' => array(
                'index',
            ),
        ),
    ),
    'student' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_account' => array(
                'edit', 'index', 'saveStudies', 'saveSubjects', 'studies', 'subjects', 'uploadProfileImage', 'preferenceMappings', 'savePreferenceMappings'
            ),
            'common_session' => array(
                'manage', 'expire',
            ),
        ),
    ),
    'editor' => array(
        'system'  => true,
        'parents' => array(),
        'actions' => array(
            'common_admin_faq' => array(
                'add', 'delete', 'edit', 'manage', 'search'
            ),
        ),
    ),
);
