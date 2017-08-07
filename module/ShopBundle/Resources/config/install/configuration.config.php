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
    array(
        'key' => 'shop.name',
        'value' => 'Theokot',
        'description' => 'The name of the shop',
    ),
    array(
        'key' => 'shop.reservation_threshold',
        'value' => 'P1D',
        'description' => 'The maximal interval before the beginning of a sales session reservations can be made',
    ),
    array(
        'key' => 'shop.reservation_default_permission',
        'value' => false,
        'description' => 'Whether every user can make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_shifts_permission_enabled',
        'value' => true,
        'description' => 'Whether doing shifts can grant one permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_shifts_unit_id',
        'value' => 2,
        'description' => 'The id of the unit for which doing shifts can grant one permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_shifts_number',
        'value' => 3,
        'description' => 'The amount of shifts one has to do for the selected unit to be granted permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_organisation_status_permission_enabled',
        'value' => true,
        'description' => 'Whether users of a certain organization status are granted permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_organisation_status_permission_status',
        'value' => 'praesidium',
        'description' => 'The status users need to have to be granted permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.reservation_shifts_general_enabled',
        'value' => true,
        'description' => 'Whether volunteers can be granted permission to make reservations based on the number of shifts they\'ve done, regardless of the unit these shifts belonged to',
    ),
    array(
        'key' => 'shop.reservation_shifts_general_number',
        'value' => 10,
        'description' => 'The amount of shifts volunteers have to do (for any unit) to be granted permission to make reservations in the shop',
    ),
    array(
        'key' => 'shop.maximal_no_shows',
        'value' => 2,
        'description' => 'The minimal amount of no-shows that revokes the permission to make reservations in the shop',
    ),
);
