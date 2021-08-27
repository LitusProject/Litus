<?php

namespace TicketBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('ticket', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'activtiy'          => Component\Validator\Activity::class,
                'Activtiy'          => Component\Validator\Activity::class,
                'bookingsclosedate' => Component\Validator\BookingsCloseDate::class,
                'bookingsCloseDate' => Component\Validator\BookingsCloseDate::class,
                'BookingsCloseDate' => Component\Validator\BookingsCloseDate::class,
                'numbertickets'     => Component\Validator\NumberTickets::class,
                'numberTickets'     => Component\Validator\NumberTickets::class,
                'NumberTickets'     => Component\Validator\NumberTickets::class,
            ),
        ),
    )
);
