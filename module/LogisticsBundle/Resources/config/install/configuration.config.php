<?php

return array(
    array(
        'key'         => 'logistics.piano_auto_confirm_immediatly',
        'value'       => '1',
        'description' => 'Automatically confirm all piano reservations',
    ),
    array(
        'key'         => 'logistics.piano_auto_confirm_deadline',
        'value'       => 'P1D',
        'description' => 'The deadline for auto confirm a piano reservation',
    ),
    array(
        'key'         => 'logistics.piano_time_slot_duration',
        'value'       => '30',
        'description' => 'Duration of one time slot for a piano reservation in minutes',
    ),
    array(
        'key'         => 'logistics.piano_time_slot_max_duration',
        'value'       => '90',
        'description' => 'Maximum duration of one time slot for a piano reservation in minutes',
    ),
    array(
        'key'         => 'logistics.piano_reservation_max_in_advance',
        'value'       => 'P30D',
        'description' => 'Maximum days a reservation is possible in advance',
    ),
    array(
        'key'         => 'logistics.piano_time_slots',
        'value'       => serialize(
            array(
                '1' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Monday
                '2' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Tuesday
                '3' => null, // Wednesday
                '4' => array(
                    array('start' => '19:00', 'end' => '22:00'),
                ), // Thursday
                '5' => null, // Friday
                '6' => null, // Saturday
                '7' => null, // Sunday
            )
        ),
        'description' => 'Available time slots for a piano reservation',
    ),
    array(
        'key'         => 'logistics.piano_mail_to',
        'value'       => 'vice@vtk.be',
        'description' => 'The mail address piano reservation mails are send to',
    ),
    array(
        'key'         => 'logistics.piano_new_reservation',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'New Piano Reservation',
                    'content' => 'Dear,

A new piano reservation was made:
{{ name }} from {{ start }} until {{ end }}.

It is important you always have the accompanying letter with you if you are going to play. You can get this in Blok 6 (Studentenwijk Arenberg) from the vice. You should be able to show this letter when security asks for it.

VTK

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Piano Reservatie',
                    'content' => 'Beste,

Een nieuwe piano reservatie is aangemaakt:
{{ name }} van {{ start }} tot {{ end }}.

Het is belangrijk dat je de begeleidende brief steeds bij je hebt als je gaat spelen. Deze kan je gaan afhalen op blok 6 (Studentenwijk Arenberg) bij de vice. De brief moet je steeds kunnen voorleggen wanneer security er om vraagt.

VTK

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a new piano reservation is created',
    ),
    array(
        'key'         => 'logistics.piano_new_reservation_confirmed',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'New Piano Reservation',
                    'content' => 'Dear,

A new piano reservation was made and confirmed:
{{ name }} from {{ start }} until {{ end }}.

It is important you always have the accompanying letter with you if you are going to play. You can get this in Blok 6 (Studentenwijk Arenberg) from the vice. You should be able to show this letter when security asks for it.

VTK

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Piano Reservatie',
                    'content' => 'Beste,

Een nieuwe piano reservatie is aangemaakt en bevestigd:
{{ name }} van {{ start }} tot {{ end }}.

Het is belangrijk dat je de begeleidende brief steeds bij je hebt als je gaat spelen. Deze kan je gaan afhalen op blok 6 (Studentenwijk Arenberg) bij de vice. De brief moet je steeds kunnen voorleggen wanneer security er om vraagt.

VTK

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a new piano reservation is created and confirmed',
    ),
    array(
        'key'         => 'logistics.icalendar_uid_suffix',
        'value'       => 'logistics.vtk.be',
        'description' => 'The suffix of an iCalendar event uid',
    ),
    array(
        'key'         => 'logistics.show_extra_text_reservation_page',
        'value'       => '0',
        'description' => 'Whether or not to show the text above the reservations.',
    ),
    array(
        'key'         => 'logistics.extra_text_reservation_page',
        'value'       => serialize(
            array(
                'en' => 'This is a placeholder text, please change me',
                'nl' => 'Deze tekst moet nog aanepast worden',
            )
        ),
        'description' => 'The additional displayed above the reservation overview.',
    ),
    array(
        'key'         => 'logistics.order_mail',
        'value'       => 'logi@vtk.be',
        'description' => 'The mail address to which notifications should be send when a new order request has been made.',
    ),
    array(
        'key'         => 'logistics.order_mail_name',
        'value'       => 'VTK Logistiek',
        'description' => '',
    ),
    array(
        'key'         => 'logistics.order_link',
        'value'       => 'https://vtk.be/admin/logistics/request/',
        'description' => '',
    ),
    array(
        'key'         => 'logistics.request_margin_hours',
        'value'       => '1',
        'description' => 'Hour margin before and after a request for overlaps',
    ),
    array(
        'key'         => 'logistics.catalog_search_max_results',
        'value'       => 50,
        'description' => '',
    ),
    array(
        'key'         => 'logistics.consumptions_search_max_results',
        'value'       => '30',
        'description' => 'The maximum number of search results shown',
    ),
    array(
        'key'         => 'logistics.article_picture_path',
        'value'       => '/_logistics/article',
        'description' => 'The path for article photo\'s',
    ),
    array(
        'key'         => 'logistics.order_request',
        'value'       => serialize(
            array(
                'subject' => 'Nieuwe Order Request {{ name }}',
                'content' => 'Beste,

Een nieuwe order request is aangemaakt:

{{ name }} ({{ type }}) van {{ person }}.
Datum: {{ start }} tot {{ end }}.

VTK IT

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
            )
        ),
        'description' => 'The mail sent when a new order request is created',
    ),
    array(
        'key'         => 'logistics.order_request_confirmed',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Order Request Approved {{ name }}',
                    'content' => 'Dear,

Your recent order request has been approved:
{{ name }}
Date: {{ start }} tot {{ end }}.

VTK IT

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Order Request Geaccepteerd {{ name }}',
                    'content' => 'Beste,

Je order request is geaccepteerd:
{{ name }}
Datum: {{ start }} tot {{ end }}.

VTK IT

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when an order request is confirmed',
    ),
    array(
        'key'         => 'logistics.order_request_rejected',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Order Request Rejected  {{ name }}',
                    'content' => 'Dear,

Your recent order request has been rejected:
{{ name }}
Date: {{ start }} tot {{ end }}.

reason: {{ reason }}

VTK IT

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Order Request Geweigerd  {{ name }}',
                    'content' => 'Beste,

Je order request is geweigerd:
{{ name }}
Datum: {{ start }} tot {{ end }}.

reden: {{ reason }}

VTK IT

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when an order request is rejected',
    ),
    array(
        'key'         => 'logistics.order_alert_mail',
        'value'       => serialize(
            array(
                'subject' => 'Nieuwe Order Request {{ name }} voor {{ article }}',
                'content' => 'Beste,

Een nieuwe order request is aangemaakt voor {{ article }}:
aantal: {{ amount }}.

{{ name }} van {{ person }}.
Datum: {{ start }} tot {{ end }}.

VTK IT

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
            )
        ),
        'description' => 'The mail sent when an article is requested that has an alert mail',
    ),
    array(
        'key'         => 'logistics.locations',
        'value'       => 'Logikot',
        'description' => 'serialized array of all possible Article locations',
    ),
);
