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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace GalleryBundle;

use CommonBundle\Component\Assetic\Filter\Css as CssFilter,
    CommonBundle\Component\Assetic\Filter\Js as JsFilter,
    CommonBundle\Component\Assetic\Filter\Less as LessFilter;

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
