<?php

namespace GalleryBundle;

use CommonBundle\Component\Assetic\Filter\Css as CssFilter;
use CommonBundle\Component\Assetic\Filter\Js as JsFilter;
use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'controllers' => array(
        'gallery_admin_gallery' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@plupload_css',
            '@plupload_js',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),

        'gallery' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@gallery_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@gallery_js',
        ),
    ),

    'collections' => array(
        'gallery_css' => array(
            'assets' => array(
                'common/less/gallery.less',
                'common/less/imageGallery.min.css',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'gallery_css.css',
            ),
        ),
        'gallery_js' => array(
            'assets' => array(
                'common/js/imageGallery.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'plupload_js' => array(
            'assets' => array(
                'plupload/js/plupload.full.js',
                'plupload/js/uploadkit/uploadkit.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'plupload_css' => array(
            'assets' => array(
                'plupload/js/uploadkit/uploadkit.css',
            ),
            'filters' => array(
                '?CssFilter' => array(
                    'name' => CssFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'plupload_css.css',
            ),
        ),
    ),
);
