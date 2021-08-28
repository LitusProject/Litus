<?php

return array(
    'guest' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'logistics_index' => array(
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
        ),
    ),
);
