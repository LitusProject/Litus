<?php

namespace FormBundle;

use CommonBundle\Component\Module\Config;
use TicketBundle\Component\Validator\NumberTicketsGuest;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('manage', 'view', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'maxtimeslots'       => Component\Validator\MaxTimeSlots::class,
                'maxTimeSlots'       => Component\Validator\MaxTimeSlots::class,
                'MaxTimeSlot'        => Component\Validator\MaxTimeSlots::class,
                'fieldrequired'      => Component\Validator\FieldRequired::class,
                'fieldRequired'      => Component\Validator\FieldRequired::class,
                'FieldRequired'      => Component\Validator\FieldRequired::class,
                'textfield'          => Component\Validator\TextField::class,
                'textField'          => Component\Validator\TextField::class,
                'TextField'          => Component\Validator\TextField::class,
                'timeslot'           => Component\Validator\TimeSlot::class,
                'timeSlot'           => Component\Validator\TimeSlot::class,
                'TimeSlot'           => Component\Validator\TimeSlot::class,
                'NumberTicketsGuest' => NumberTicketsGuest::class,
            ),
        ),
    )
);
