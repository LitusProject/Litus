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

return array(
    'submenus' => array(
        'Logistics' => array(
            'subtitle' => array('Drivers', 'Lease', 'Reservations'),
            'items'    => array(
                'logistics_admin_driver'            => array('title' => 'Drivers'),
                'logistics_admin_lease'             => array('title' => 'Lease'),
                'logistics_admin_piano_reservation' => array('title' => 'Piano Reservations'),
                'logistics_admin_van_reservation'   => array('title' => 'Van Reservations'),
            ),
        ),
    ),
);
