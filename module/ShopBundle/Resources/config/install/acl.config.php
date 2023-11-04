<?php

return array(
    'shopbundle' => array(
        'shop' => array(
            'index', 'reserve', 'reservations', 'deleteReservation', 'reserveproducts', 'consume', 'reward',
        ),
        'shop_admin_shop_product' => array(
            'manage', 'add', 'edit', 'delete', 'search',
        ),
        'shop_admin_shop_salessession' => array(
            'manage', 'add', 'edit', 'delete', 'old', 'search', 'oldsearch',
        ),
        'shop_admin_shop_reservation' => array(
            'salessession', 'csv', 'delete', 'noshow', 'search',
        ),
        'shop_admin_shop_ban' => array(
            'manage', 'old', 'delete', 'add', 'search',
        ),
        'shop_admin_shop_reservationpermission' => array(
            'manage', 'delete', 'add', 'togglepermission', 'search',
        ),
        'shop_admin_shop_message' => array(
            'manage', 'delete', 'add', 'edit',
        ),
        'shop_admin_shop_openinghour' => array(
            'add', 'edit', 'schedule', 'delete', 'manage', 'old',
        ),
    ),
);
