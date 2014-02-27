<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
            'add', 'edit', 'manage', 'search', 'typeahead'
        ),
        'syllabus_admin_subject_study' => array(
            'add', 'delete', 'edit'
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
