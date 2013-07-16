<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'controllers'  => array(
        'mail_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'mail_admin_alias' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_remote_typeahead',
        ),
        'mail_admin_bakske' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'mail_admin_group' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'mail_admin_list' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_remote_typeahead',
        ),
        'mail_admin_message' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'mail_admin_prof' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'mail_admin_study' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'mail_admin_volunteer' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        )
    ),
    'routes' => array(),
);
