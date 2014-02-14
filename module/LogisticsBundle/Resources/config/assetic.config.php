<?php

return array(
    'controllers'  => array(
        'logistics_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'logistics_admin_driver' => array(
            '@common_jquery',
            '@common_remote_typeahead',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@minicolor_css',
            '@minicolor_js',
        ),
        'logistics_admin_van_reservation' => array(
            '@common_jquery',
            '@common_remote_typeahead',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'logistics_admin_lease' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'logistics_admin_piano_reservation' => array(
            '@common_jquery',
            '@common_remote_typeahead',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'logistics_index' => array(
            '@common_jquery',
            '@common_jqueryui',
            '@common_remote_typeahead',
            '@common_jquery_form',
            '@fullcalendar_css',
            '@logistics_js',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@logistics_css',
        ),
        'logistics_lease' => array(
            '@common_jquery',
            '@common_jqueryui',
            '@common_remote_typeahead',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@logistics_css',
        ),
        'logistics_piano' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@bootstrap_js_tab',
        ),
    ),

    'collections' => array(
        'logistics_css' => array(
            'assets' => array(
                'logistics/less/base.less',
            ),
            'filters' => array(
                'logistics_less' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Less',
                ),
            ),
            'options' => array(
                'output' => 'logistics_css.css',
            ),
        ),
        'fullcalendar_css' => array(
            'assets' => array(
                'logistics/fullcalendar/fullcalendar.css',
            ),
            'filters' => array(
                'fullcalendar_css_yui' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Css',
                ),
            ),
            'options' => array(
                'output' => 'fullcalendar_css.css',
            ),
        ),
        'logistics_js' => array(
            'assets' => array(
                'logistics/js/logistics.js',
                'logistics/fullcalendar/fullcalendar.js',
            ),
            'filters' => array(
                'logistics_js_yui' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                ),
            ),
        ),
        'minicolor_css' => array(
            'assets' => array(
                'logistics/minicolor/jquery.miniColors.css',
            ),
            'filters' => array(
                'minicolor_css_yui' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Css',
                ),
            ),
            'options' => array(
                'output' => 'minicolor_css.css',
            ),
        ),
        'minicolor_js' => array(
            'assets' => array(
                'logistics/minicolor/jquery.miniColors.min.js',
            ),
            'filters' => array(
                'minicolor_js_yui' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                ),
            ),
        ),
    ),
);
