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
    'submenus' => array(
        'Syllabus' => array(
            'subtitle'    => array('Groups', 'Studies', 'Subjects'),
            'items'       => array(
                'syllabus_admin_academic' => array(
                    'action' => 'manage',
                    'title'  => 'Academics',
                ),
                'syllabus_admin_group'    => array(
                    'action' => 'manage',
                    'title'  => 'Groups',
                ),
                'syllabus_admin_study'    => array(
                    'action' => 'manage',
                    'title'  => 'Studies',
                ),
                'syllabus_admin_subject'  => array(
                    'action' => 'manage',
                    'title'  => 'Subjects',
                ),
                'syllabus_admin_update'   => array(
                    'action' => 'index',
                    'title'  => 'Update',
                ),
            ),
            'controllers' => array(
                'syllabus_admin_subject_comment',
                'syllabus_admin_prof',
            ),
        ),
    ),
);
