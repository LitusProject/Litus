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
            'invokables' => array(
                'prom_code_exists'      => 'PromBundle\Component\Validator\CodeExists',
                'prom_code_used'        => 'PromBundle\Component\Validator\CodeUsed',
                'prom_code_email'       => 'PromBundle\Component\Validator\CodeEmail',
                'prom_passenger_exists' => 'PromBundle\Component\Validator\PassengerExists',
                'prom_bus_selected'     => 'PromBundle\Component\Validator\BusSelected',
                'prom_bus_seats'        => 'PromBundle\Component\Validator\BusSeats',
            ),
        ),
    )
);
