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
    'mailbundle' => array(
        'mail_admin_alias' => array(
            'manage', 'add', 'delete', 'search'
        ),
        'mail_admin_bakske' => array(
            'send'
        ),
        'mail_admin_group' => array(
            'groups', 'send'
        ),
        'mail_admin_list' => array(
            'manage', 'add', 'entries', 'admins', 'delete', 'deleteAdmin', 'deleteAdminRole', 'deleteAllEntries', 'deleteEntry', 'search'
        ),
        'mail_admin_message' => array(
            'manage', 'edit', 'delete'
        ),
        'mail_admin_prof' => array(
            'cudi'
        ),
        'mail_admin_study' => array(
            'send'
        ),
        'mail_admin_volunteer' => array(
            'send'
        ),
    ),
);
