<?php

return array(
    'logisticsbundle' => array(
        'logistics_admin_driver' => array(
            'add', 'delete', 'edit', 'manage'
        ),
        'logistics_admin_piano_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'logistics_admin_van_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'logistics_admin_lease' => array(
            'add', 'delete', 'edit', 'manage'
        ),
        'logistics_index' => array(
            'add', 'delete', 'edit', 'export', 'fetch', 'index', 'move'
        ),
        'logistics_lease' => array(
            'availabilityCheck', 'history', 'index', 'show', 'typeahead'
        ),
        'logistics_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
        'logistics_piano' => array(
            'index'
        ),
    ),
);
