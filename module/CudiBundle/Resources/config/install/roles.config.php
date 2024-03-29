<?php

return array(
    'guest' => array(
        'system'  => true,
        'actions' => array(
            'cudi_prof_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'cudi_sale_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'cudi_opening_hour' => array(
                'week',
            ),
        ),
    ),
    'supplier' => array(
        'system'  => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_supplier_article' => array(
                'manage',
            ),
            'cudi_supplier_index' => array(
                'index',
            ),
        ),
    ),
    'prof' => array(
        'system'  => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_prof_article' => array(
                'add', 'addFromSubject', 'edit', 'manage', 'typeahead',
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
                'manage', 'subject', 'typeahead',
            ),
        ),
    ),
    'student' => array(
        'system'  => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_booking' => array(
                'book', 'bookSearch', 'cancel', 'keepUpdated', 'search', 'view',
            ),
        ),
    ),
);
