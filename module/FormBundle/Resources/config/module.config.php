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

namespace FormBundle;

use CommonBundle\Component\Module\Config;

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
                'maxtimeslots'  => Component\Validator\MaxTimeSlots::class,
                'maxTimeSlots'  => Component\Validator\MaxTimeSlots::class,
                'MaxTimeSlot'   => Component\Validator\MaxTimeSlots::class,
                'fieldrequired' => Component\Validator\FieldRequired::class,
                'fieldRequired' => Component\Validator\FieldRequired::class,
                'FieldRequired' => Component\Validator\FieldRequired::class,
                'textfield'     => Component\Validator\TextField::class,
                'textField'     => Component\Validator\TextField::class,
                'TextField'     => Component\Validator\TextField::class,
                'timeslot'      => Component\Validator\TimeSlot::class,
                'timeSlot'      => Component\Validator\TimeSlot::class,
                'TimeSlot'      => Component\Validator\TimeSlot::class,
            ),
        ),
    )
);
