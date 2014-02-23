<?php

return array(
    'ticketbundle' => array(
        'ticket_admin_event' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'ticket_admin_ticket' => array(
            'export', 'manage', 'print'
        ),
        'ticket_sale_index' => array(
            'sale', 'validate'
        ),
        'ticket_sale_ticket' => array(
            'delete', 'overview', 'sale', 'unassign', 'undoSale'
        ),
        'ticket_sale_person' => array(
            'typeahead'
        ),
        'ticket' => array(
            'delete', 'event'
        ),
    ),
);
