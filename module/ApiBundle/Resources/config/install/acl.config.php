<?php

return array(
    'apibundle' => array(
        'api_admin_key' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'api_auth' => array(
            'getCorporate', 'getPerson', 'me'
        ),
        'api_br' => array(
            'add-company','edit-company-name', 'add-cv-book',  'add-page-visible', 'is-page-visible', 'get-cv-years', 'get-company-id', 'send-activation', 'add-user', 'get-user-id', 'getSubscriptions',
        ),
        'api_calendar' => array(
            'activeEvents', 'poster',
        ),
        'api_commu' => array(
            'get-cudi-openinghours', 'get-events',
        ),
        'api_config' => array(
            'entries',
        ),
        'api_cudi' => array(
            'articles', 'book', 'bookings', 'cancelBooking', 'currentSession', 'openingHours', 'signIn', 'signInStatus', 'is-same',
        ),
        'api_door' => array(
            'getRules', 'log', 'get-username', 'is-allowed',
        ),
        'api_fak' => array(
            'add-checkin', 'add-checkin-username',
        ),
        'api_mail' => array(
            'getAliases', 'getLists', 'getListsArchive',
        ),
        'api_member' => array(
            'all',
        ),
        'api_news' => array(
            'all',
        ),
        'api_oauth' => array(
            'authorize', 'shibboleth', 'token',
        ),
        'api_shift' => array(
            'active', 'responsible', 'volunteer', 'signOut',
        ),
    ),
);
