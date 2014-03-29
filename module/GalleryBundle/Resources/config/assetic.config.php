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
            'filters' => array('less'),
            'options' => array(
                'output' => 'gallery_css.css',
            ),
        ),
        'gallery_js' => array(
            'assets'  => array(
                'common/js/imageGallery.js',
            ),
            'filters' => array('js'),
        ),
        'plupload_js' => array(
            'assets'  => array(
                'plupload/js/plupload.full.js',
                'plupload/js/bootstrap/uploadkit.js',
            ),
            'filters' => array('js'),
        ),
        'plupload_css' => array(
            'assets'  => array(
                'plupload/js/bootstrap/uploadkit.css',
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'plupload_css.css',
            ),
        ),
    ),
);
