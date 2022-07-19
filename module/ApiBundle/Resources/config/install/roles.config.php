<?php

return array(
    'guest' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'api_oauth' => array(
                'authorize', 'shibboleth',
            ),
            'api_cudi' => array(
                'isSame', 'is-same',
            ),
            'api_br' => array(
                'addCompany', 'editCompanyName', 'addCvBook', 'addPageVisible', 'isPageVisible','getCvYears',
            ),
        ),
    ),
    'student' => array(
        'system'  => true,
        'parents' => array(
        ),
        'actions' => array(
            'api_cudi' => array(
                'articles', 'book', 'bookings', 'cancelBooking', 'currentSession', 'signIn', 'signInStatus',
            ),
            'api_shift' => array(
                'active', 'responsible', 'volunteer', 'signOut',
            ),
        ),
    ),
);
