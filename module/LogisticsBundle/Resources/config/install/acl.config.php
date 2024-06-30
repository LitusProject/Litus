<?php

return array(
    'logisticsbundle' => array(
        'logistics_admin_driver' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'logistics_admin_piano_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old',
        ),
        'logistics_admin_article' => array(
            'add', 'delete', 'edit', 'manage', 'search', 'typeahead', 'uploadImage', 'orders', 'csv', 'template',
        ),
        'logistics_admin_order' => array(
            'add', 'delete', 'edit', 'manage', 'removed', 'articles', 'deleteArticle', 'articleMapping', 'approveArticle', 'reviewOrder', 'approve', 'reject', 'view',
        ),
        'logistics_admin_request' => array(
            'manage', 'approved', 'conflicting', 'old', 'coming', 'csv', 'export',
        ),
        'logistics_admin_van_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old',
        ),
        'logistics_admin_lease' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'logistics_admin_inventory' => array(
            'add', 'manage',
        ),
        'logistics_transport' => array(
            'add', 'delete', 'edit', 'export', 'fetch', 'index', 'move',
        ),
        'logistics_lease' => array(
            'history', 'index', 'show', 'typeahead',
        ),
        'logistics_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
        'logistics_piano' => array(
            'index',
        ),
        'logistics_catalog' => array(
            'addOrder', 'editOrder', 'overview', 'catalog', 'cancelRequest', 'removeRequest', 'editMap', 'deleteMap', 'view', 'search', 'inventory',
        ),
        'logistics_inventory' => array(
            'index', 'add', 'edit', 'reserve',
        ),
        'logistics_order' => array(
            'index', 'view', 'add', 'edit', 'cancel', 'remove',
        ),
        'logistics_inventory_article' => array(
            'index', 'add', 'edit', 'search', 'addArticles', 'editArticles', 'searchArticles',
        ),
        'logistics_flesserke_article' => array(
            'index', 'add', 'edit', 'search', 'addArticles', 'editArticles', 'searchArticles',
        ),
    ),
);
