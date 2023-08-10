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
            'add', 'delete', 'edit', 'manage', 'search', 'typeahead', 'uploadImage', 'orders'
        ),
        'logistics_admin_order' => array(
            'add', 'delete', 'edit', 'manage', 'removed', 'articles', 'deleteArticle', 'articleMapping', 'approveArticle', 'conflicting', 'review', 'approve', 'reject'
        ),
        'logistics_admin_request' => array(
            'reject', 'approve', 'manage', 'view',
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
        'logistics_index' => array(
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
            'addOrder', 'editOrder', 'overview', 'catalog', 'cancelRequest', 'removeRequest', 'editMap', 'deleteMap', 'view', 'search'
        ),
        'logistics_inventory' => array(
            'index', 'add', 'edit', 'reserve',
        )
    ),
);
