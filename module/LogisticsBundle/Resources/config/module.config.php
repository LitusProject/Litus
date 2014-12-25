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
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('logistics'),
        'has_layouts'       => true,
        'has_documents'     => true,
    ),
    array(
        'validators' => array(
            'invokables' => array(
                'logistics_typeahead_driver'           => 'LogisticsBundle\Component\Validator\Typeahead\Driver',
                'logistics_typeahead_lease'            => 'LogisticsBundle\Component\Validator\Typeahead\Lease',
                'logistics_piano_duration'             => 'LogisticsBundle\Component\Validator\PianoDuration',
                'logistics_piano_reservation_conflict' => 'LogisticsBundle\Component\Validator\PianoReservationConflict',
                'logistics_reservation_conflict'       => 'LogisticsBundle\Component\Validator\ReservationConflict',
            ),
        ),
    )
);
