<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('logistics', 'validator'),
        'has_layouts'       => true,
        'has_documents'     => true,
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
                'typeaheadlease'           => Component\Validator\Typeahead\Lease::class,
                'typeaheadLease'           => Component\Validator\Typeahead\Lease::class,
                'TypeaheadLease'           => Component\Validator\Typeahead\Lease::class,
            ),
        ),
    )
);
