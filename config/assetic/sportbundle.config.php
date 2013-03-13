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
        'sport_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'sport_admin_run' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@run_js',
            '@common_socket',
        ),
        'sport_run_index' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@run_css',
            '@run_js',
        ),
        'sport_run_group' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@run_css',
            '@run_js',
        ),
        'sport_run_queue' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@run_css',
            '@run_js',
            '@common_socket',
        ),
        'sport_run_screen' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@run_css',
            '@run_js',
            '@common_socket',
        ),
    ),
    'routes' => array(),
);
