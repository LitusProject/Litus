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
        'form_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_form' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@gollum_css',
            '@gollum_js',
            '@admin_css',
        ),
        'admin_form_field' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
        ),
        'admin_form_viewer' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@common_typeahead_remote',
        ),
        'form_view' => array(
            '@common_jquery',
            '@common_fieldcount',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'form_manage' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_alert',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@form_manage_css',
        ),
    ),
    'routes' => array(),
);
