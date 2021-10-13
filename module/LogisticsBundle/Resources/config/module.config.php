<?php

namespace LogisticsBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('logistics', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'pianoduration'            => Component\Validator\PianoDuration::class,
                'pianoDuration'            => Component\Validator\PianoDuration::class,
                'PianoDuration'            => Component\Validator\PianoDuration::class,
                'pianoreservationconflict' => Component\Validator\PianoReservationConflict::class,
                'pianoReservationConflict' => Component\Validator\PianoReservationConflict::class,
                'PianoReservationConflict' => Component\Validator\PianoReservationConflict::class,
                'reservationconflict'      => Component\Validator\ReservationConflict::class,
                'reservationConflict'      => Component\Validator\ReservationConflict::class,
                'ReservationConflict'      => Component\Validator\ReservationConflict::class,
                'typeaheaddriver'          => Component\Validator\Typeahead\Driver::class,
                'typeaheadDriver'          => Component\Validator\Typeahead\Driver::class,
                'TypeaheadDriver'          => Component\Validator\Typeahead\Driver::class,
                'typeaheadlease'           => Component\Validator\Typeahead\Lease::class,
                'typeaheadLease'           => Component\Validator\Typeahead\Lease::class,
                'TypeaheadLease'           => Component\Validator\Typeahead\Lease::class,
            ),
        ),
    )
);
