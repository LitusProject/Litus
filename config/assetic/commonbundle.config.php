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
    'controllers' => array(
        'common_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'all_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),

        'common_admin_academic' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_auth' => array(
            '@common_jquery',
        ),
        'common_admin_config' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_serialize_js',
        ),
        'common_admin_cache' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'common_admin_index' => array(
            '@common_jquery',
            '@common_chart_js',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_location' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_person' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_role' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'common_admin_unit' => array(
            '@common_jquery',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@admin_css',
            '@common_remote_typeahead',
        ),

        'common_account' => array_merge(
            $base_site,
            array(
                '@bootstrap_js_tooltip',
                '@bootstrap_js_popover',
                '@bootstrap_js_modal',
                '@secretary_css',
                '@common_download_file',
                '@common_remote_typeahead',
                '@common_holder_js',
            )
        ),
        'common_session' => $base_site,
        'common_auth' => array_merge(
            $base_site,
            array(
                '@bootstrap_js_tooltip',
                '@bootstrap_js_popover',
            )
        ),
        'common_index' => array_merge(
            $base_site,
            array(
                '@bootstrap_js_tooltip',
                '@bootstrap_js_popover',
            )
        ),
        'common_praesidium' => array_merge(
            $base_site,
            array(
                '@common_holder_js',
            )
        ),
    ),
    'routes' => array(),
);
