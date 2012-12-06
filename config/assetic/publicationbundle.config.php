<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'controllers'  => array(
        'publication_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'admin_publication' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'admin_edition_pdf' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_form_upload_progress',
            '@common_download_file',
            '@common_permanent_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'admin_edition_html' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_form_upload_progress',
            '@common_download_file',
            '@common_permanent_modal',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'archive' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_responsive_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
            '@archive_css',
        ),
        'edition_html' => array(
        ),
    ),
    'routes' => array(),
);
