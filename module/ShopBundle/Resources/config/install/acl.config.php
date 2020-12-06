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

return array(
    'shopbundle' => array(
        'shop' => array(
            'index', 'reserve', 'reservations', 'deleteReservation', 'reserveproducts',
        ),
        'shop_admin_shop_product' => array(
            'manage', 'add', 'edit', 'delete', 'search',
        ),
        'shop_admin_shop_salessession' => array(
            'manage', 'add', 'edit', 'delete', 'old', 'search', 'oldsearch',
        ),
        'shop_admin_shop_reservation' => array(
            'salessession', 'csv', 'delete', 'noshow', 'search',
        ),
        'shop_admin_shop_reservationpermission' => array(
            'manage', 'delete', 'add', 'togglepermission', 'search',
        ),
    ),
);
