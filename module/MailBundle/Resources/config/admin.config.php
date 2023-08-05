<?php

return array(
    'submenus' => array(
        'Mail' => array(
            'subtitle' => array('Aliases', 'Lists', 'Newsletter', 'Mass Mail'),
            'items'    => array(
                'mail_admin_alias' => array(
                    'action' => 'manage',
                    'title'  => 'Aliases',
                ),
                'mail_admin_bakske' => array(
                    'action' => 'send',
                    'title'  => 'Het Bakske',
                ),
                'mail_admin_section' => array(
                    'action' => 'manage',
                    'title'  => 'Newsletter',
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
