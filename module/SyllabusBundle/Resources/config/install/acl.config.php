<?php

return array(
    'syllabusbundle' => array(
        'syllabus_admin_prof' => array(
            'add', 'delete', 'typeahead'
        ),
        'syllabus_admin_academic' => array(
            'addStudy', 'addSubject', 'deleteStudy', 'deleteSubject', 'edit', 'manage', 'search'
        ),
        'syllabus_admin_group' => array(
            'add', 'delete', 'deleteStudy', 'edit', 'export', 'manage', 'studies'
        ),
        'syllabus_admin_study' => array(
            'edit', 'manage', 'search', 'searchSubject', 'typeahead'
        ),
        'syllabus_admin_subject' => array(
            'edit', 'manage', 'search', 'typeahead'
        ),
        'syllabus_admin_subject_comment' => array(
            'delete', 'manage', 'subject', 'reply'
        ),
        'syllabus_admin_update' => array(
            'index', 'updateNow'
        ),
        'syllabus_subject' => array(
            'typeahead'
        ),
    ),
);
