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
        'form_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'form_admin_form' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@gollum_css',
            '@gollum_js',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'form_admin_group' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jquery_table_sort',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'form_admin_form_field' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
            '@common_jqueryui',
            '@common_jquery_table_sort',
        ),
        'form_admin_form_viewer' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@common_remote_typeahead',
        ),
        'form_admin_group_viewer' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@common_remote_typeahead',
        ),
        'form_view' => array(
            '@common_jquery',
            '@common_fieldcount',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'form_group' => array(
            '@common_jquery',
            '@common_fieldcount',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
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
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@form_manage_css',
            '@common_jquery_form',
            '@common_remote_typeahead',
        ),
        'form_manage_group' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_alert',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_tab',
            '@form_manage_css',
            '@common_jquery_form',
        ),
    ),

    'collections' => array(
        'form_manage_css' => array(
            'assets' => array(
                'manage/less/base.less',
            ),
            'filters' => array('less'),
            'options' => array(
                'output' => 'form_manage_css.css',
            ),
        ),
    ),
);
