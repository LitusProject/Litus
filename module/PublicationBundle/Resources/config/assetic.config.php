<?php

namespace PublicationBundle;

use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'controllers' => array(
        'publication_admin_publication' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'publication_admin_edition_pdf' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_jquery_form',
            '@common_form_upload_progress',
            '@common_permanent_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'publication_admin_edition_html' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_jquery_form',
            '@common_form_upload_progress',
            '@common_permanent_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'publication_archive' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@archive_css',
        ),
        'publication_edition_html' => array(
        ),
    ),

    'collections' => array(
        'archive_css' => array(
            'assets' => array(
                'archive/less/archive.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'archive_css.css',
            ),
        ),
    ),
);
