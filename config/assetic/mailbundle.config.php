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
        'admin_mail' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_mail_bakske' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_mail_prof' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_mail_study' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_mail_bakske' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
    ),
    'routes' => array(),
);
