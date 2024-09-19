<?php

namespace CommonBundle;

use CommonBundle\Component\Assetic\Filter\Css as CssFilter;
use CommonBundle\Component\Assetic\Filter\Js as JsFilter;
use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'controllers' => array(
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
            '@common_serialize_js',
        ),
        'common_admin_cache' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'common_admin_index' => array(
            '@common_jquery',
            '@common_chart_js',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
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
        'common_admin_visit' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_faq' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jquery_form',
            '@common_form_upload_progress',
            '@gollum_css',
            '@gollum_js',
            '@common_remote_typeahead',
        ),

        'common_account' => array(
            '@bootstrap_css',
            '@site_css',
            '@shift_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_modal',
            '@secretary_css',
            '@common_remote_typeahead',
            '@common_holder_js',
            '@jcrop_js',
            '@jcrop_css',
            '@common_jquery_form',
        ),
        'common_session' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'common_auth' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
        ),
        'common_contact' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'common_index' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@common_holder_js',

        ),
        'common_praesidium' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@common_holder_js',
        ),
        'common_poc' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@common_holder_js',
        ),
        'common_privacy' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
    ),

    'collections' => array(
        'common_jquery' => array(
            'assets' => array(
                'common/js/jquery.min.js',
                'common/js/bootstrap-fileinput.min.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_jqueryui' => array(
            'assets' => array(
                'common/js/jquery-ui.min.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_jqueryui_css' => array(
            'assets' => array(
                'common/css/jquery-ui.min.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'jquery-ui.min.css',
            ),
        ),
        'common_jqueryui_datepicker' => array(
            'assets' => array(
                'common/js/jquery-ui-timepicker-addon.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_jqueryui_datepicker_css' => array(
            'assets' => array(
                'common/css/jquery-ui-timepicker-addon.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'jquery-ui-timepicker-addon.css',
            ),
        ),
        'common_jquery_table_sort' => array(
            'assets' => array(
                'common/js/jquery.sortable-table.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_jquery_form' => array(
            'assets' => array(
                'common/js/jquery.form.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_form_upload_progress' => array(
            'assets' => array(
                'common/js/formUploadProgress.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_permanent_modal' => array(
            'assets' => array(
                'common/js/permanentModal.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_socket' => array(
            'assets' => array(
                'common/js/socket.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_fieldcount' => array(
            'assets' => array(
                'common/js/fieldcount.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_remote_typeahead' => array(
            'assets' => array(
                'common/js/typeaheadRemote.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_spin_js' => array(
            'assets' => array(
                'common/js/spin.min.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_holder_js' => array(
            'assets' => array(
                'common/js/holder.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_chart_js' => array(
            'assets' => array(
                'common/js/chart.min.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'common_serialize_js' => array(
            'assets' => array(
                'common/js/serialize.js',
                'common/js/unserialize.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),

        'admin_css' => array(
            'assets' => array(
                'admin/less/base.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'admin_css.css',
            ),
        ),
        'admin_js' => array(
            'assets' => array(
                'admin/js/*.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),

        'display_form_error_js' => array(
            'assets' => array(
                'site/js/displayFormErrors.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),

        // 'dropdown_hover_js' => array(
        //     'assets' => array(
        //         'site/js/dropdownHover.js',
        //     ),
        //     'filters' => array(
        //         '?JsFilter' => array(
        //             'name' => JsFilter::class,
        //         ),
        //     ),
        // ),

        'site_css' => array(
            'assets' => array(
                'site/less/base.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'site_css.css',
            ),
        ),

        'bootstrap_js_rowlink' => array(
            'assets' => array(
                'common/js/bootstrap-rowlink.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),

        'gollum_css' => array(
            'assets' => array(
                'gollum/css/editor.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'gollum_css.css',
            ),
        ),
        'gollum_js' => array(
            'assets' => array(
                'gollum/js/editor.js',
                'gollum/js/markdown.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),

        'jcrop_js' => array(
            'assets' => array(
                'common/js/jcrop.min.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'jcrop_css' => array(
            'assets' => array(
                'common/css/jcrop.min.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'jcrop_css.css',
            ),
        ),

        'resizableColumns_js' => array(
            'assets' => array(
                'common/js/resizableColumns.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'resizableColumns_css' => array(
            'assets' => array(
                'common/css/resizableColumns.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'resizableColumns_css.css',
            ),
        ),
    ),
);
