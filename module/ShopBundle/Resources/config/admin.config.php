<?php

return array(
    'submenus' => array(
        'Shop' => array(
            'subtitle'    => array('Products', 'Sessions', 'Reservations'),
            'items'       => array(
                'shop_admin_shop_product' => array(
                    'action' => 'manage',
                    'title'  => 'Products',
                ),
                'shop_admin_shop_salessession' => array(
                    'action' => 'manage',
                    'title'  => 'Sales Sessions',
                ),
                'shop_admin_shop_reservationpermission' => array(
                    'action' => 'manage',
                    'title'  => 'Permissions',
                ),
                'shop_admin_shop_message' => array(
                    'action' => 'manage',
                    'title'  => 'Message',
                ),

            ),
            'controllers' => array('shop_admin_shop'),
        ),
    ),
);
