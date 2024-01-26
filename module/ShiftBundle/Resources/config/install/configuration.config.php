<?php

return array(
    array(
        'key'         => 'shift.signout_treshold',
        'value'       => 'P1D',
        'description' => 'The date interval after which a person cannot sign out from a shift',
        'published'   => true,
    ),
    array(
        'key'         => 'shift.responsible_signout_treshold',
        'value'       => 'PT12H',
        'description' => 'The date interval after which a responsible cannot be signed out from a shift',
    ),
    array(
        'key'         => 'shift.mail',
        'value'       => 'it@vtk.be',
        'description' => 'The mail address from which shift notifications are sent',
    ),
    array(
        'key'         => 'shift.mail_name',
        'value'       => 'VTK IT',
        'description' => 'The name of the mail address from which shift notifications are sent',
    ),
    array(
        'key'         => 'shift.praesidium_removed_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Shift Signout',
                    'content' => 'Dear,

You have been removed from the following shift by a non-praesidium volunteer:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Shift Afmelding',
                    'content' => 'Beste,

U bent verwijderd van de volgende shift door een niet-praesidium vrijwilliger:
{{ shift }}

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent to a praesidium member when a volunteer removes him from a shift.',
    ),
    array(
        'key'         => 'shift.shift_deleted_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Shift Deleted',
                    'content' => 'Dear,

The following shift to which you were subscribed has been deleted:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Shift Verwijderd',
                    'content' => 'Beste,

De volgende shift waar je was op ingeschreven is verwijderd:
{{ shift }}

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent to a shift subscriber when the shift is deleted.',
    ),
    array(
        'key'         => 'shift.subscription_deleted_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Shift Signout',
                    'content' => 'Dear,

You have been removed from the following shift by an administrator:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Shift Afmelding',
                    'content' => 'Beste,

U bent verwijderd van de volgende shift door een administrator:
{{ shift }}

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent to a shift subscriber when he is removed from the shift.',
    ),
    array(
        'key'         => 'shift.pdf_generator_path',
        'value'       => 'data/shift/pdf_generator',
        'description' => 'The path to the PDF generator files',
    ),
    array(
        'key'         => 'shift.ranking_criteria',
        'value'       => serialize(
            array(
                array(
                    'name'  => 'silver',
                    'limit' => '10',
                ),
                array(
                    'name'  => 'gold',
                    'limit' => '20',
                ),
            )
        ),
        'description' => 'The ranking criteria for volunteers',
    ),
    array(
        'key'         => 'shift.icalendar_uid_suffix',
        'value'       => 'shift.vtk.be',
        'description' => 'The suffix of an iCalendar shift uid',
    ),
    array(
        'key'         => 'shift.reward_numbers',
        'value'       => serialize(
            array(
                '2'  => 2,
                '0'  => 0,
                '3'  => 3,
                '10' => 10,
            )
        ),
        'description' => 'The coins you can select as reward for a shift',
    ),
    array(
        'key'         => 'shift.insurance_enabled',
        'value'       => 0,
        'description' => 'A flag if the insurance reading feature is enabled.',
    ),
    array(
        'key'         => 'shift.insurance_text',
        'value'       => array(
            'nl' => 'Vul deze config in!',
            'en' => 'Fill this config in!',
        ),
        'description' => 'The insurance text to be read.',
    ),
    array(
        'key'         => 'shift.hours_per_shift',
        'value'       => '0',
        'description' => 'Shift amount is counted in blocks of this amount of hours. If 0, then it counts the amount of shifts.',
    ),
    array(
        'key'         => 'shift.rewards_enabled',
        'value'       => '1',
        'description' => 'Enables the shifts reward system. Rewards are payable coins counted by the system per shift.',
    ),
    array(
        'key'         => 'shift.points_enabled',
        'value'       => '0',
        'description' => 'Enables the shifts points systems. Points are a non-payable fictive measurement to build the shift ranking. When enabling the ranking will no longer be based on the amount of shifts, but on the points per shift.',
    ),
    array(
        'key'         => 'shift.enable_registration_shifts_button_homepage',
        'value'       => '1',
        'description' => 'Enable the registration shifts button on the homepage',
    ),
    array(
        'key'         => 'shift.weekly_change_interval',
        'value'       => 'P1W',
        'description' => 'Set how far the weekly change goes back',
    ),
    array(
        'key'         => 'shift.praesidium_counter_interval',
        'value'       => 'P1W',
        'description' => 'Set how far the praesidium future counter goes forward',
    ),
    array(
        'key'         => 'shift.praesidium_counter_start_day',
        'value'       => 'Monday this week',
        'description' => 'Set the starting day for the praesidium counter',
    ),
);
