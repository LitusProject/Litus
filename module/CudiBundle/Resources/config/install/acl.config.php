<?php

return array(
    'cudibundle' => array(
        'cudi_admin_article' => array(
            'add', 'convertToExternal', 'convertToInternal', 'delete', 'duplicate', 'edit', 'history', 'manage', 'search',
        ),
        'cudi_admin_article_comment' => array(
            'delete', 'manage',
        ),
        'cudi_admin_article_file' => array(
            'delete', 'download', 'edit', 'front', 'manage', 'upload',
        ),
        'cudi_admin_article_subject' => array(
            'delete', 'manage',
        ),
        'cudi_admin_mail' => array(
            'send',
        ),
        'cudi_admin_prof_action' => array(
            'completed', 'confirmArticle', 'confirmFile', 'confirm', 'manage', 'refused', 'refuse', 'view',
        ),
        'cudi_admin_sales_article' => array(
            'activate', 'add', 'assignAll', 'cancelBookings', 'delete', 'download', 'edit', 'export', 'history', 'mail', 'manage', 'search', 'typeahead', 'view',
        ),
        'cudi_admin_sales_article_sale' => array(
            'sale',
        ),
        'cudi_admin_sales_article_barcode' => array(
            'delete', 'manage',
        ),
        'cudi_admin_sales_booking' => array(
            'actions', 'add', 'article', 'assign', 'assignAll', 'delete', 'deleteAll', 'edit', 'extendAll', 'expire', 'expireAll', 'extend', 'inactive', 'manage', 'person', 'return', 'search', 'unassign', 'undo',
        ),
        'cudi_admin_sales_article_discount' => array(
            'delete', 'manage',
        ),
        'cudi_admin_sales_article_discount_template' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'cudi_admin_sales_article_restriction' => array(
            'delete', 'manage',
        ),
        'cudi_admin_sales_financial' => array(
            'export', 'overview', 'period',
        ),
        'cudi_admin_sales_financial_delivered' => array(
            'article', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'supplierSearch', 'supplier', 'suppliers',
        ),
        'cudi_admin_sales_financial_ordered' => array(
            'individualSearch', 'individual', 'orderSearch', 'order', 'orders', 'ordersSearch', 'supplierSearch', 'supplier', 'suppliers',
        ),
        'cudi_admin_sales_financial_sold' => array(
            'article', 'articleSearch', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'sessionSearch', 'session', 'sessions', 'supplierSearch', 'supplier', 'suppliers',
        ),
        'cudi_admin_sales_financial_returned' => array(
            'article', 'articleSearch', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'sessionSearch', 'session', 'sessions',
        ),
        'cudi_admin_sales_session' => array(
            'add', 'close', 'edit', 'editRegister', 'manage', 'queueItems',
        ),
        'cudi_admin_sales_session_restriction' => array(
            'delete', 'manage',
        ),
        'cudi_admin_sales_session_openinghour' => array(
            'add', 'edit', 'schedule', 'delete', 'manage', 'old',
        ),
        'cudi_admin_sales_session_message' => array(
            'add', 'edit', 'delete', 'manage',
        ),
        'cudi_admin_special_action' => array(
            'manage', 'irreeel',
        ),
        'cudi_admin_stock' => array(
            'bulkUpdate', 'delta', 'download', 'edit', 'export', 'manage', 'notDelivered', 'search', 'searchNotDelivered', 'view',
        ),
        'cudi_admin_stock_delivery' => array(
            'add', 'delete', 'manage', 'supplier', 'typeahead',
        ),
        'cudi_admin_stock_order' => array(
            'add', 'cancel', 'csv', 'delete', 'deleteAll','edit', 'editItem', 'export', 'manage', 'overview', 'place', 'pdf', 'search', 'supplier', 'delivered',
        ),
        'cudi_admin_stock_period' => array(
            'manage', 'new', 'search', 'view', 'revertNew',
        ),
        'cudi_admin_stock_retour' => array(
            'add', 'delete', 'manage', 'supplier',
        ),
        'cudi_admin_supplier' => array(
            'add', 'edit', 'manage',
        ),
        'cudi_admin_supplier_user' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'cudi_admin_syllabus' => array(
            'manage', 'list', 'articlescsv',
        ),
        'cudi_admin_isic' => array(
            'manage','assign', 'unassign', 'delete',
        ),
        'cudi_isic' => array(
            'form','order',
        ),
        'cudi_prof_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
        'cudi_prof_article' => array(
            'add', 'addFromSubject', 'delete', 'edit', 'manage', 'typeahead',
        ),
        'cudi_prof_article_mapping' => array(
            'activate', 'add', 'delete',
        ),
        'cudi_prof_article_comment' => array(
            'delete', 'manage',
        ),
        'cudi_prof_subject_comment' => array(
            'delete', 'manage',
        ),
        'cudi_prof_file' => array(
            'delete', 'download', 'manage', 'upload',
        ),
        'cudi_prof_index' => array(
            'index',
        ),
        'cudi_prof_prof' => array(
            'add', 'delete', 'typeahead',
        ),
        'cudi_prof_subject' => array(
            'all', 'manage', 'subject', 'typeahead',
        ),
        'cudi_prof_help' => array(
            'index',
        ),
        'cudi_sale_queue' => array(
            'overview', 'screen', 'signin',
        ),
        'cudi_sale_sale' => array(
            'return', 'returnPrice', 'sale', 'getBoughtItems',
        ),
        /*'cudi_sale_return' => array(
            'return', 'getBoughtItems',
        ),*/
        'cudi_sale_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
        'cudi_supplier_article' => array(
            'manage',
        ),
        'cudi_supplier_index' => array(
            'index',
        ),
        'cudi_booking' => array(
            'book', 'bookSearch', 'cancel', 'keepUpdated', 'search', 'view',
        ),
        'cudi_opening_hour' => array(
            'week',
        ),
        'cudi_admin_retail' => array(
            'add', 'delete', 'edit', 'manage', 'search', 'articleTypeahead',
        ),
        'cudi_retail' => array(
            'overview', 'deal', 'myDeals', 'myRetails', 'deleteRetail', 'deleteDeal','articleTypeahead', 'recommendedRetails',
        ),
        'cudi_printer' => array(
            'view', 'pay', 'payed',
        ),
    ),
);
