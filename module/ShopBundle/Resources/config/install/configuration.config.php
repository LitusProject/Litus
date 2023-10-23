<?php

return array(
    array(
        'key'         => 'shop.name',
        'value'       => 'Theokot',
        'description' => 'The name of the shop',
    ),
    array(
        'key'         => 'shop.email',
        'value'       => '',
        'description' => 'The name of the shop',
    ),
    array(
        'key'         => 'shop.reservation_threshold',
        'value'       => 'P1D',
        'description' => 'The maximal interval before the beginning of a sales session reservations can be made',
    ),
    array(
        'key'         => 'shop.reservation_default_permission',
        'value'       => false,
        'description' => 'Whether every user can make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_shifts_permission_enabled',
        'value'       => true,
        'description' => 'Whether doing shifts can grant one permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_shifts_unit_id',
        'value'       => 2,
        'description' => 'The id of the unit for which doing shifts can grant one permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_shifts_number',
        'value'       => 3,
        'description' => 'The amount of shifts one has to do for the selected unit to be granted permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_organisation_status_permission_enabled',
        'value'       => true,
        'description' => 'Whether users of a certain organization status are granted permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_organisation_status_permission_status',
        'value'       => 'praesidium',
        'description' => 'The status users need to have to be granted permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.reservation_shifts_general_enabled',
        'value'       => true,
        'description' => 'Whether volunteers can be granted permission to make reservations based on the number of shifts they\'ve done, regardless of the unit these shifts belonged to',
    ),
    array(
        'key'         => 'shop.reservation_shifts_general_number',
        'value'       => 10,
        'description' => 'The amount of shifts volunteers have to do (for any unit) to be granted permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.maximal_no_shows',
        'value'       => 2,
        'description' => 'The minimal amount of no-shows that revokes the permission to make reservations in the shop',
    ),
    array(
        'key'         => 'shop.enable_shop_button_homepage',
        'value'       => 1,
        'description' => 'Enable the shop/reservation button on the homepage',
    ),
    array(
        'key'         => 'shop.url_reservations',
        'value'       => 'https://www.vtk.be/shop',
        'description' => 'The URL of the shop',
    ),
    array(
        'key'         => 'shop.enable_winner',
        'value'       => 1,
        'description' => 'Enable the winner column when exporting a sales session to csv',
    ),
    array(
        'key'         => 'shop.no_show_configuration',
        'value'       => 'no-show configuration goes here',
        'description' => 'The no-show configuration is used for warnings when a person does not show up for their reservation, holding for each warning the ban period and an email message',
    ),
);
