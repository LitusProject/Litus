<?php

return array(
    'guest' => array(
        'system'  => true,
        'actions' => array(
            'secretary_registration' => array(
                'add',
            ),
        ),
    ),
    'student' => array(
        'system'  => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'secretary_registration' => array(
                'add', 'complete', 'edit', 'saveStudies', 'saveSubjects', 'studies', 'subjects', 'preferences', 'savePreferences'
            ),
        ),
    ),
);
