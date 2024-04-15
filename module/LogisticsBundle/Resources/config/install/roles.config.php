<?php

return array(
    'guest' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'logistics_transport' => array(
                'fetch', 'index',
            ),
            'logistics_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'logistics_piano' => array(
                'index',
            ),
            'logistics_catalog' => array(
                'overview',
            ),
            'logistics_inventory' => array(
                'index',
            ),
            'logistics_order' => array(
                'index',
            ),
            'logistics_inventory_article' => array(
                'index', 'search',
            ),
        ),
    ),
);
