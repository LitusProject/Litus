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
