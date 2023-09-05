<?php

return array(
    'ticketbundle' => array(
        'ticket_admin_event' => array(
            'add', 'delete', 'edit', 'manage', 'old', 'clean', 'salesgraph', 'exportSalesgraph', 'clearVisitors',
        ),
        'ticket_admin_ticket' => array(
            'export', 'manage', 'print', 'search', 'csv',
        ),
        'ticket_admin_consumptions' => array(
            'add', 'delete', 'edit', 'manage', 'search', 'consume', 'transactions',
            'total-transactions', 'delete_all', 'csv', 'template', 'search_transaction',
            'export',
        ),
        'ticket_sale_index' => array(
            'sale', 'validate',
        ),
        'ticket_sale_ticket' => array(
            'delete', 'overview', 'sale', 'unassign', 'undoSale', 'search',
        ),
        'ticket_sale_person' => array(
            'typeahead',
        ),
        'ticket_sale_consume' => array(
            'consume',
        ),
        'ticket' => array(
            'delete', 'event', 'pay', 'payed', 'pay-response', 'scanQr', 'qr'
        ),
    ),
);
