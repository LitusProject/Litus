<?php

return array(
    'routes' => array(
        'shop_admin_shop' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop[/:action[/:id]]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'shift_admin_shift',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop_admin_shop_product' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/product[/:action[/:id][/:field[/:string]][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_product',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop_admin_shop_salessession' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/salessession[/:action[/:id][:field[/:string]][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_salessession',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop_admin_shop_reservation' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/reservation[/:action[/:id]][/:field/:string][/type/:type][/page/:page][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'type'   => '[a-zA-Z]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_reservation',
                    'action'     => 'salessession',
                ),
            ),
        ),
        'shop_admin_shop_reservationpermission' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/reservationpermission[/:action[/:id][/type/:type][/page/:page]][/:field/:string][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'type'   => '[a-zA-Z]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_reservationpermission',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop_admin_shop_message' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/message[/:action[/:id][/page/:page]][/:field/:string][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_message',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop_admin_shop_openinghour' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/shop/openinghours[/:action[/page/:page][/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop_admin_shop_openinghour',
                    'action'     => 'manage',
                ),
            ),
        ),
        'shop' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/shop[/:action[/:id]][/]',
                'constraints' => array(
                    'language' => '(en|nl)',
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'shop',
                    'action'     => 'reserve',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'shop_admin_shop_salessession'          => 'ShopBundle\Controller\Admin\SalesSessionController',
        'shop_admin_shop_product'               => 'ShopBundle\Controller\Admin\ProductController',
        'shop_admin_shop_reservation'           => 'ShopBundle\Controller\Admin\ReservationController',
        'shop_admin_shop_reservationpermission' => 'ShopBundle\Controller\Admin\ReservationPermissionController',
        'shop_admin_shop_message'               => 'ShopBundle\Controller\Admin\MessageController',
        'shop_admin_shop_openinghour '          => 'ShopBundle\Controller\Admin\OpeningHourController',
        'shop'                                  => 'ShopBundle\Controller\ShopController',
    ),
);
