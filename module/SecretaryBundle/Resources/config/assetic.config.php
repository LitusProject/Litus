<?php

namespace SecretaryBundle;

use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'controllers' => array(
        'secretary_admin_registration' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_admin_export' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_admin_photos' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_admin_promotion' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_registration' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@secretary_css',
            '@common_remote_typeahead',
        ),
        'secretary_admin_working_group' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_remote_typeahead',
        ),
        'secretary_admin_pull' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_remote_typeahead',
        ),
        'secretary_pull' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@common_remote_typeahead',
        ),
    ),

    'collections' => array(
        'secretary_css' => array(
            'assets' => array(
                'secretary/less/study.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'secretary.css',
            ),
        ),
    ),
);
