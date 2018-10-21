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

namespace PromBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('prom'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'busselected'     => Component\Validator\BusSelected::class,
                'busSelected'     => Component\Validator\BusSelected::class,
                'BusSelected'     => Component\Validator\BusSelected::class,
                'busseats'        => Component\Validator\BusSeats::class,
                'busSeats'        => Component\Validator\BusSeats::class,
                'BusSeats'        => Component\Validator\BusSeats::class,
                'codeemail'       => Component\Validator\CodeEmail::class,
                'codeEmail'       => Component\Validator\CodeEmail::class,
                'CodeEmail'       => Component\Validator\CodeEmail::class,
                'codeexists'      => Component\Validator\CodeExists::class,
                'codeExists'      => Component\Validator\CodeExists::class,
                'CodeExists'      => Component\Validator\CodeExists::class,
                'codeused'        => Component\Validator\CodeUsed::class,
                'codeUsed'        => Component\Validator\CodeUsed::class,
                'CodeUsed'        => Component\Validator\CodeUsed::class,
                'passengerexists' => Component\Validator\PassengerExists::class,
                'passengerExists' => Component\Validator\PassengerExists::class,
                'PassengerExists' => Component\Validator\PassengerExists::class,
            ),
        ),
    )
);
