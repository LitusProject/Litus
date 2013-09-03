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
    'controllers' => array(
        'common_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'all_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),

        'common_admin_academic' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_auth' => array(
            '@common_jquery',
        ),
        'common_admin_config' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_cache' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'common_admin_index' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'common_admin_location' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_person' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_role' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_unit' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@admin_css',
            '@common_remote_typeahead',
        ),

        'common_account' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@flaty_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@bootstrap_js_modal',
            '@secretary_css',
            '@common_remote_typeahead',
            '@common_holder_js',
        ),
        'common_session' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@flaty_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'common_auth' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@flaty_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'common_index' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@flaty_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
    ),
    'routes' => array(),
);
