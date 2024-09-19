<?php

return array(
    'shiftbundle' => array(
        'shift_admin_shift' => array(
            'add', 'delete', 'edit', 'export', 'manage', 'old', 'pdf', 'search', 'template', 'csv', 'event',
        ),
        'shift_admin_registration_shift' => array(
            'add', 'delete', 'edit', 'manage', 'old', 'search', 'csv',
        ),
        'shift_admin_shift_counter' => array(
            'delete', 'export', 'index', 'payed', 'payout', 'search', 'units', 'view', 'praesidium', 'totalPayed',
        ),
        'shift_admin_shift_ranking' => array(
            'index',
        ),
        'shift_admin_shift_weekly_change' => array(
            'index',
        ),
        'shift_admin_shift_subscription' => array(
            'manage', 'delete', 'superdelete', 'superadd',
        ),
        'shift_admin_registration_shift_subscription' => array(
            'manage', 'delete', 'superdelete', 'superadd',
        ),
        'shift' => array(
            'export', 'history', 'index', 'responsible', 'signOut', 'volunteer',
        ),
        'registration_shift' => array(
            'history', 'index', 'registered', 'signOut',
        ),
    ),
);
