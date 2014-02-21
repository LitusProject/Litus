<?php

return array(
    'prof' => array(
        'system' => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
        ),
    ),
    'guest' => array(
        'system' => true,
        'actions' => array(
            'syllabus_subject' => array(
                'typeahead'
            ),
        ),
    ),
);
