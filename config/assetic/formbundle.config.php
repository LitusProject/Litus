<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'controllers'  => array(
        'form_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),

        'admin_form' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_modal',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@gollum_css',
            '@gollum_js',
            '@admin_css',
        ),

        'admin_form_field' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_modal',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@gollum_css',
            '@gollum_js',
            '@admin_css',
        ),
    ),
    'routes' => array(),
);
