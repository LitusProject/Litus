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
        'Mail' => array(
            'subtitle' => array('Aliases', 'Lists', 'Mass Mail'),
            'items'    => array(
                'mail_admin_alias' => array(
                    'action' => 'manage',
                    'title'  => 'Aliases',
                ),
                'mail_admin_bakske' => array(
                    'action' => 'send',
                    'title'  => 'Het Bakske',
                ),
                'mail_admin_group' => array(
                    'action' => 'groups',
                    'title'  => 'Groups',
                ),
                'mail_admin_list' => array(
                    'action' => 'manage',
                    'title'  => 'Lists',
                ),
                'mail_admin_prof' => array(
                    'action' => 'cudi',
                    'title'  => 'Prof',
                ),
                'mail_admin_promotion' => array(
                    'action' => 'send',
                    'title'  => 'Promotions',
                ),
                'mail_admin_message' => array(
                    'action' => 'manage',
                    'title'  => 'Stored Messages',
                ),
                'mail_admin_study' => array(
                    'action' => 'send',
                    'title'  => 'Studies',
                ),
                'mail_admin_volunteer' => array(
                    'action' => 'send',
                    'title'  => 'Volunteers',
                ),
            ),
        ),
    ),
);
