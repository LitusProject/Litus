<?php

namespace CalendarBundle;

use Assetic\Filter\LessFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\UglifyJs3Filter;

return array(
    'controllers' => array(
        'calendar_admin_calendar' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_permanent_modal',
            '@common_jquery_form',
            '@common_form_upload_progress',
            '@gollum_css',
            '@gollum_js',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'calendar_admin_calendar_registration' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'calendar' => array(
            '@bootstrap_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@calendar_css',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_modal',
            '@calendar_js',
            '@common_spin_js',
        ),
    ),

    'collections' => array(
        'calendar_css' => array(
            'assets' => array(
                'calendar/less/calendar.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'calendar_css.css',
            ),
        ),
        'calendar_js' => array(
            'assets' => array(
                'calendar/js/calendar.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
    ),
);
