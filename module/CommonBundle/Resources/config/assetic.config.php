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

        'common_account' => array(
            '@bootstrap_css',
            '@flatly_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
            '@bootstrap_js_modal',
            '@secretary_css',
            '@common_download_file',
            '@common_remote_typeahead',
            '@common_holder_js',
            '@jcrop_js',
            '@jcrop_css',
            '@common_jquery_form',
        ),
        'common_session' => array(
            '@bootstrap_css',
            '@flatly_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',
        ),
        'common_auth' => array(
            '@bootstrap_css',
            '@flatly_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
        ),
        'common_index' => array(
            '@bootstrap_css',
            '@flatly_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@bootstrap_js_tooltip',
            '@bootstrap_js_popover',
        ),
        'common_praesidium' => array(
            '@bootstrap_css',
            '@flatly_css',
            '@site_css',
            '@common_jquery',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_alert',

            '@common_holder_js',
        ),
    ),

    'collections' => array(
        'common_jquery' => array(
            'assets'  => array(
                'common/js/jquery.min.js',
                'common/js/bootstrap-fileinput.min.js',
            ),
            'filters' => array('js'),
        ),
        'common_jqueryui' => array(
            'assets'  => array(
                'common/js/jquery-ui.min.js',
            ),
            'filters' => array('js'),
        ),
        'common_jqueryui_css' => array(
            'assets' => array(
                'common/css/jquery-ui.min.css',
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'jquery-ui.min.css',
            ),
        ),
        'common_jqueryui_datepicker' => array(
            'assets' => array(
                'common/js/jquery-ui-timepicker-addon.js',
            ),
            'filters' => array('js'),
        ),
        'common_jqueryui_datepicker_css' => array(
            'assets' => array(
                'common/css/jquery-ui-timepicker-addon.css',
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'jquery-ui-timepicker-addon.css',
            ),
        ),
        'common_jquery_table_sort' => array(
            'assets' => array(
                'common/js/jquery.sortable-table.js'
            ),
            'filters' => array('js'),
        ),
        'common_jquery_form' => array(
            'assets'  => array(
                'common/js/jquery.form.js',
            ),
            'filters' => array('js'),
        ),
        'common_form_upload_progress' => array(
            'assets'  => array(
                'common/js/formUploadProgress.js',
            ),
            'filters' => array('js'),
        ),
        'common_permanent_modal' => array(
            'assets'  => array(
                'common/js/permanentModal.js',
            ),
            'filters' => array('js'),
        ),
        'common_socket' => array(
            'assets'  => array(
                'common/js/socket.js',
            ),
            'filters' => array('js'),
        ),
        'common_download_file' => array(
            'assets'  => array(
                'common/js/downloadFile.js',
            ),
            'filters' => array('js'),
        ),
        'common_fieldcount' => array(
            'assets'  => array(
                'common/js/fieldcount.js',
            ),
            'filters' => array('js'),
        ),
        'common_remote_typeahead' => array(
            'assets'  => array(
                'common/js/typeaheadRemote.js',
            ),
            'filters' => array('js'),
        ),
        'common_spin_js' => array(
            'assets'  => array(
                'common/js/spin.min.js',
            ),
            'filters' => array('js'),
        ),
        'common_holder_js' => array(
            'assets'  => array(
                'common/js/holder.js',
            ),
            'filters' => array('js'),
        ),
        'common_chart_js' => array(
            'assets'  => array(
                'common/js/chart.min.js',
            ),
            'filters' => array('js'),
        ),
        'common_serialize_js' => array(
            'assets'  => array(
                'common/js/serialize.js',
                'common/js/unserialize.js',
            ),
            'filters' => array('js'),
        ),

        'admin_css' => array(
            'assets' => array(
                'admin/less/admin.less',
            ),
            'filters' => array('less'),
            'options' => array(
                'output' => 'admin_css.css',
            ),
        ),
        'admin_js' => array(
            'assets'  => array(
                'admin/js/*.js',
            ),
            'filters' => array('js'),
        ),

        'site_css' => array(
            'assets' => array(
                'site/less/base.less',
            ),
            'filters' => array('less'),
            'options' => array(
                'output' => 'site_css.css'
            ),
        ),

        'bootstrap_js_rowlink' => array(
            'assets'  => array(
                'common/js/bootstrap-rowlink.js',
            ),
            'filters' => array('js'),
        ),

        'gollum_css' => array(
            'assets' => array(
                'gollum/css/editor.css'
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'gollum_css.css',
            ),
        ),
        'gollum_js' => array(
            'assets' => array(
                'gollum/js/editor.js',
                'gollum/js/markdown.js',
            ),
            'filters' => array('js'),
        ),

        'jcrop_js' => array(
            'assets' => array(
                'common/js/jcrop.min.js',
            ),
            'filters' => array('js'),
        ),
        'jcrop_css' => array(
            'assets' => array(
                'common/css/jcrop.min.css',
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'jcrop_css.css',
            ),
        ),

        'flatly_css' => array(
            'assets' => array(
                'site/css/flatly.css',
            ),
            'filters' => array('css'),
            'options' => array(
                'output' => 'flatly_css.css'
            ),
        ),
    ),
);
