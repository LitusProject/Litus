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
        'quiz_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'quiz_admin_quiz' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_admin_team' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_admin_round' => array(
            '@common_jquery',
            '@common_jqueryui',
            '@common_jquery_table_sort',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_quiz' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@quiz_css',
        )
    ),
    'routes' => array(),
);
