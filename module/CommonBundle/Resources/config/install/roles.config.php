<?php

return array(
    'guest' => array(
        'system' => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_admin_auth' => array(
                'authenticate', 'login', 'logout', 'shibboleth'
            ),
            'common_auth' => array(
                'login', 'logout', 'shibboleth'
            ),
            'common_index' => array(
                'index'
            ),
            'common_account' => array(
                'activate'
            ),
            'common_robots' => array(
                'index'
            ),
        ),
    ),
    'student' => array(
        'system' => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_account' => array(
                'edit', 'index', 'saveStudies', 'saveSubjects', 'studies', 'subjects',
            ),
        ),
    ),
);
