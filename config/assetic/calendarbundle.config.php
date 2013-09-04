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
        'calendar_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'calendar_admin_calendar' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_permanent_modal',
            '@common_jquery_form',
            '@common_form_upload_progress',
            '@common_download_file',
            '@gollum_css',
            '@gollum_js',
            '@admin_css',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'calendar_admin_calendar_registration' => array(
            '@common_jquery',
            '@admin_js',
            '@admin_css',
            '@common_download_file',
        ),
        'calendar' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@flaty_css',
            '@bootstrap_responsive_css',
            '@calendar_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@calendar_js',
            '@common_spin_js',
        ),
    ),
    'routes' => array(),
);
