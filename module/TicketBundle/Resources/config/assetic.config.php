<?php

namespace TicketBundle;

use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

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
            '@common_remote_typeahead',
            '@admin_js',
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
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'ticket_css.css',
            ),
        ),
    ),
);
