<?php

return array(
    'submenus' => array(
        'Syllabus' => array(
            'subtitle'    => array('Groups', 'Studies', 'Subjects'),
            'items'       => array(
                'syllabus_admin_academic' => array(
                    'action' => 'manage',
                    'title'  => 'Academics',
                ),
                'syllabus_admin_group' => array(
                    'action' => 'manage',
                    'title'  => 'Groups',
                ),
                'syllabus_admin_poc' => array(
                    'action' => 'manage',
                    'title'  => 'POC',
                ),
                'syllabus_admin_study' => array(
                    'action' => 'manage',
                    'title'  => 'Studies',
                ),
                'syllabus_admin_study_module_group' => array(
                    'action' => 'manage',
                    'title'  => 'Module Groups',
                ),
                'syllabus_admin_subject' => array(
                    'action' => 'manage',
                    'title'  => 'Subjects',
                ),
                'syllabus_admin_update' => array(
                    'action' => 'index',
                    'title'  => 'Update',
                ),
            ),
            'controllers' => array(
                'syllabus_admin_subject_comment',
                'syllabus_admin_subject_prof',
                'syllabus_admin_subject_module_group',
            ),
        ),
    ),
);
