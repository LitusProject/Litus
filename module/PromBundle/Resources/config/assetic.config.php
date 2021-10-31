<?php

namespace PromBundle;

use Assetic\Filter\LessFilter;

return array(
    'controllers' => array(
        'prom_admin_bus' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'prom_admin_code' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'prom_admin_passenger' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'prom_registration_index' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@display_form_error_js',
            '@registration_css',
            '@common_remote_typeahead',
        ),
    ),

    'collections' => array(
        'registration_css' => array(
            'assets' => array(
                'registration/less/base.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'registration_css.css',
            ),
        ),
    ),
);
