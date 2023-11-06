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
                'shop_admin_shop_ban' => array(
                    'action' => 'manage',
                    'title'  => 'Bans',
                ),
                'shop_admin_shop_message' => array(
                    'action' => 'manage',
                    'title'  => 'Message',
                ),
                'shop_admin_shop_openinghour' => array(
                    'action' => 'manage',
                    'title' => 'Opening Hours',
                    'help'  => 'Manage the opening hours of Theokot. These opening hours will be shown on the website.',
                ),

            ),
            'controllers' => array('shop_admin_shop'),
        ),
    ),
);
