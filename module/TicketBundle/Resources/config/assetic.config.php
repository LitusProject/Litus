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

return array(
    'controllers' => array(
        'ticket_admin_event' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_jqueryui',
            '@common_jqueryui_datepicker',
            '@common_jqueryui_css',
            '@common_jqueryui_datepicker_css',
        ),
        'ticket_admin_ticket' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@resizableColumns_js',
            '@resizableColumns_css',
        ),
        'ticket_sale_index' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@display_form_error_js',
            '@ticket_css',
            '@common_remote_typeahead',
        ),
        'ticket_sale_ticket' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_alert',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@ticket_css',
        ),
        'ticket' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@bootstrap_js_carousel',
            '@bootstrap_js_alert',
        ),
    ),

    'collections' => array(
        'ticket_css' => array(
            'assets' => array(
                'ticket/less/base.less',
            ),
            'filters' => array('less'),
            'options' => array(
                'output' => 'ticket_css.css',
            ),
        ),
    ),
);
