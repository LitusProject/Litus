<?php

return array(
    'controllers' => array(
        'page_admin_page' => array(
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
        'page_admin_category' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'page_admin_link' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),

        'page' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@page_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
    ),

    'collections' => array(
        'page_css' => array(
            'assets' => array(
                'page/less/page.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'page_css.css',
            ),
        ),
    ),
);
