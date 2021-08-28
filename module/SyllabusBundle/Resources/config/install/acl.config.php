<?php

return array(
    'syllabusbundle' => array(
        'syllabus_admin_academic' => array(
            'addStudy', 'addSubject', 'deleteStudy', 'deleteSubject', 'edit', 'manage', 'search',
        ),
        'syllabus_admin_group' => array(
            'add', 'delete', 'deleteStudy', 'edit', 'export', 'manage', 'studies',
        ),
        'syllabus_admin_poc' => array(
            'members', 'delete', 'manage','deleteMember','editEmail',
        ),
        'syllabus_admin_study' => array(
            'add', 'delete', 'edit', 'manage', 'search', 'searchSubject', 'typeahead', 'view',
        ),
        'syllabus_admin_study_module_group' => array(
            'add', 'edit', 'manage', 'search', 'searchSubject', 'typeahead', 'view',
        ),
        'syllabus_admin_subject' => array(
            'add', 'edit', 'manage', 'search', 'typeahead', 'view',
        ),
        'syllabus_admin_subject_comment' => array(
            'delete', 'manage', 'reply', 'subject',
        ),
        'syllabus_admin_subject_module_group' => array(
            'add', 'delete', 'edit',
        ),
        'syllabus_admin_subject_prof' => array(
            'add', 'delete', 'typeahead',
        ),
        'syllabus_admin_update' => array(
            'index', 'updateNow',
        ),
        'syllabus_subject' => array(
            'typeahead',
        ),
    ),
);
