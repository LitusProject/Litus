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
    'controllers'  => array(
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

    'collections' => array(
        'run_css' => array(
            'assets' => array(
                'run/less/base.less',
            ),
            'filters' => array('less'),
            'options' => array(
                'output' => 'run_css.css',
            ),
        ),
        'run_js' => array(
            'assets' => array(
                'run/js/*.js',
            ),
            'filters' => array('js'),
        ),
    ),
);
