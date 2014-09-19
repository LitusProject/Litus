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
    array(
        'key'         => 'cudi.file_path',
        'value'       => 'data/cudi/files',
        'description' => 'The path to the cudi article files',
    ),
    array(
        'key'         => 'cudi.pdf_generator_path',
        'value'       => 'data/cudi/pdf_generator',
        'description' => 'The path to the PDF generator files',
    ),
    array(
        'key'         => 'cudi.front_page_cache_dir',
        'value'       => 'data/cache/article',
        'description' => 'The path to the article front page cache files',
    ),
    array(
        'key'         => 'fop_command',
        'value'       => '/usr/local/bin/fop',
        'description' => 'The command to call Apache FOP',
    ),
    array(
        'key'         => 'search_max_results',
        'value'       => '30',
        'description' => 'The maximum number of search results shown',
    ),
    array(
        'key'         => 'cudi.mail',
        'value'       => 'cudi@vtk.be',
        'description' => 'The mail address of cudi',
    ),
    array(
        'key'         => 'cudi.mail_name',
        'value'       => 'VTK Cursusdienst',
        'description' => 'The name of the mail sender',
    ),
    array(
        'key'         => 'cudi.name',
        'value'       => 'Cudi',
        'description' => 'The name of the cudi',
    ),
    array(
        'key'         => 'cudi.person',
        'value'       => '1',
        'description' => 'The ID of the person responsible for the cudi',
    ),
    array(
        'key'         => 'cudi.delivery_address_name',
        'value'       => 'VTK Cursusdienst',
        'description' => 'The name of the delivery address of the cudi',
    ),
    array(
        'key'         => 'cudi.delivery_address_extra',
        'value'       => '(inrit via Celestijnenlaan)',
        'description' => 'The extra information of the delivery address of the cudi',
    ),
    array(
        'key'         => 'cudi.billing_address_name',
        'value'       => 'VTK vzw',
        'description' => 'The name of the billing organization of the cudi',
    ),
    array(
        'key'         => 'cudi.reservation_expire_time',
        'value'       => 'P2W',
        'description' => 'The time after which a reservation expires',
    ),
    array(
        'key'         => 'cudi.reservation_extend_time',
        'value'       => 'P2W',
        'description' => 'The time a reservation can be extended',
    ),
    array(
        'key'         => 'cudi.booking_assigned_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'New Assignments',
                    'content' => 'Dear,

The following bookings are assigned to you:
{{ bookings }}#expires#expires on#expires#

These reservations will expire after the first sale session after its expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

The opening hours of Cudi are:
{{ openingHours }}#no_opening_hours#No opening hours known.#no_opening_hours#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties zijn aan u toegewezen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn:
{{ openingHours }}#no_opening_hours#Geen openingsuren gekend.#no_opening_hours#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                ),
            )
        ),
        'description' => 'The mail sent when a booking is assigned'
    ),
    array(
        'key'         => 'cudi.booking_expire_warning_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Assignment Expiration Warning',
                    'content' => 'Dear,

The following bookings are going to expire soon:
{{ bookings }}#expires#expires on#expires#

These reservations will expire after the first sale session after its expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

The opening hours of Cudi are:
{{ openingHours }}#no_opening_hours#No opening hours known.#no_opening_hours#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                ),
                'nl' => array(
                    'subject' => 'Waarschuwing Vervallen Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties gaan binnekort vervallen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn:
{{ openingHours }}#no_opening_hours#Geen openingsuren gekend.#no_opening_hours#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                ),
            )
        ),
        'description' => 'The mail sent when a booking is about to expire'
    ),
    array(
        'key'         => 'cudi.booking_expire_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Assignment Expiration',
                    'content' => 'Dear,

The following bookings have expired:
{{ bookings }}#expires#expired on#expires#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                ),
                'nl' => array(
                    'subject' => 'Vervallen Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties zijn vervallen:
{{ bookings }}#expires#verviel op#expires#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                ),
            )
        ),
        'description' => 'The mail sent when a booking is expired'
    ),
    array(
        'key'         => 'cudi.queue_item_barcode_prefix',
        'value'       => '988000000000',
        'description' => 'The start for a serving queue item barcode',
    ),
    array(
        'key'         => 'cudi.queue_socket_file',
        'value'       => 'tcp://127.0.0.1:8899',
        'description' => 'The file used for the websocket of the queue',
    ),
    array(
        'key'         => 'cudi.queue_socket_public',
        'value'       => '127.0.0.1:8899',
        'description' => 'The public address for the websocket of the queue',
    ),
    array(
        'key'         => 'cudi.queue_socket_key',
        'value'       => md5(uniqid(rand(), true)),
        'description' => 'The key used for the websocket of the queue',
    ),
    array(
        'key'         => 'cudi.purchase_prices',
        'value'       => serialize(
            array(
                'binding_glued'     => 81620,
                'binding_stapled'   => 6360,
                'binding_none'      => 19080,
                'recto_bw'          => 2862,
                'recto_verso_bw'    => 2862,
                'recto_color'       => 6360,
                'recto_verso_color' => 10600,
                'hardcover'         => 0,
            )
        ),
        'description' => 'The purchase prices of an internal article (multiplied by 100 000)',
    ),
    array(
        'key'         => 'cudi.sell_prices',
        'value'       => serialize(
            array(
                'binding_glued'     => 83000,
                'binding_stapled'   => 7000,
                'binding_none'      => 20000,
                'recto_bw'          => 2000,
                'recto_verso_bw'    => 2000,
                'recto_color'       => 7000,
                'recto_verso_color' => 7000,
                'hardcover'         => 0,
            )
        ),
        'description' => 'The purchase prices of an internal article (multiplied by 100000)',
    ),
    array(
        'key'         => 'cudi.front_address_name',
        'value'       => 'CuDi VTK vzw',
        'description' => 'The name of the address on the front of an article',
    ),
    array(
        'key'         => 'cudi.article_barcode_prefix',
        'value'       => '978',
        'description' => 'The start for a serving queue item barcode',
    ),
    array(
        'key'         => 'cudi.enable_collect_scanning',
        'value'       => '1',
        'description' => 'Enable scanning of collected items before selling',
    ),
    array(
        'key'         => 'cudi.enable_automatic_assignment',
        'value'       => '1',
        'description' => 'Enable automatic assignment of bookings',
    ),
    array(
        'key'         => 'cudi.enable_automatic_expire',
        'value'       => '1',
        'description' => 'Enable automatic expire of bookings',
    ),
    array(
        'key'         => 'cudi.enable_bookings',
        'value'       => '1',
        'description' => 'Enable users to create bookings',
    ),
    array(
        'key'         => 'cudi.print_socket_address',
        'value'       => '127.0.0.1',
        'description' => 'The ip address of the print socket',
    ),
    array(
        'key'         => 'cudi.print_socket_port',
        'value'       => '4445',
        'description' => 'The port of the print socket',
    ),
    array(
        'key'         => 'cudi.enable_printers',
        'value'       => '1',
        'description' => 'Flag whether the printers are enabled',
    ),
    array(
        'key'         => 'cudi.printer_socket_key',
        'value'       => md5(uniqid(rand(), true)),
        'description' => 'The key used for printing',
    ),
    array(
        'key'         => 'cudi.ticket_title',
        'value'       => 'Litus Cursusdienst',
        'description' => 'The title printed on a ticket',
    ),
    array(
        'key'         => 'cudi.printers',
        'value'       => serialize(
            array(
                'signin'    => 'LITUS-SignIn',
                'collect_1' => 'LITUS-Collect',
                'collect_2' => 'LITUS-Collect',
                'collect_3' => 'LITUS-Collect',
                'paydesk_1' => 'LITUS-SaleOne',
                'paydesk_2' => 'LITUS-SaleTwo',
                'paydesk_3' => 'LITUS-SaleThree',
            )
        ),
        'description' => 'The names of the printers',
    ),
    array(
        'key'         => 'cudi.tshirt_article',
        'value'       => serialize(
            array(
                'F_S'   => 232,
                'F_M'   => 233,
                'F_L'   => 234,
                'F_XL'  => 235,

                'M_S'   => 228,
                'M_M'   => 229,
                'M_L'   => 230,
                'M_XL'  => 231,
            )
        ),
        'description' => 'The T-shirt articles',
    ),
    array(
        'key'         => 'cudi.registration_articles',
        'value'       => serialize(
            array()
        ),
        'description' => 'The articles assigned at registration',
    ),
    array(
        'key'         => 'cudi.bookings_closed_exceptions',
        'value'       => serialize(
            array()
        ),
        'description' => 'The articles assigned at registration',
    ),
    array(
        'key'         => 'cudi.number_queue_items',
        'value'       => '50',
        'description' => 'The number of queue items shown in sale app',
    ),
    array(
        'key'         => 'cudi.opening_hours_page',
        'value'       => '0',
        'description' => 'The id of the opening hour page',
    ),
    array(
        'key'         => 'cudi.expiration_warning_interval',
        'value'       => 'P4D',
        'description' => 'The interval for sending a warning mail before expiring a booking',
    ),
    array(
        'key'         => 'cudi.catalog_update_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Catalog Updates',
                    'content' => 'Dear,

The catalog of our cudi has been updated:
{{ updates }}#bookable#is now bookable#bookable# #unbookable#is not bookable anymore#unbookable# #added#is added to the catalog#added# #removed#is removed from the catalog#removed#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                ),
                'nl' => array(
                    'subject' => 'Catalogus Aanpassingen',
                    'content' => 'Beste,

De catalogus van onze cudi is aangepast:
{{ updates }}#bookable#is nu reserveerbaar#bookable# #unbookable#is niet meer reserveerbaar#unbookable# #added#is toegevoegd aan de catalogus#added# #removed#is verwijderd van de catalogus#removed#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                ),
            )
        ),
        'description' => 'The content of the mail send for catalog updates',
    ),
    array(
        'key'         => 'cudi.sale_light_version',
        'value'       => '0',
        'description' => 'Flag whether to show the light version of the sale app (no queue)',
    ),
    array(
        'key'         => 'cudi.order_job_id',
        'value'       => 'vtk-{{ date }}',
        'description' => 'The job id for a XML exported order',
    ),
    array(
        'key'         => 'cudi.booking_mails_to_cudi',
        'value'       => '1',
        'description' => 'Send the cudi booking mails (assigned, expired, warning) to the cudi address',
    ),
    array(
        'key'         => 'cudi.location',
        'value'       => serialize(
            array(
                'latitude'  => 50.8612181,
                'longitude' => 4.6837506,
            )
        ),
        'description' => 'The coordinates of the cudi',
        'published'   => true,
    ),
    array(
        'key'         => 'cudi.maximum_signin_distance',
        'value'       => '50',
        'description' => 'The maximum distance the user can be from the cudi to be able to sign in (in meters)',
        'published'   => true,
    ),
    array(
        'key'         => 'cudi.booking_only_member',
        'value'       => '0',
        'description' => 'Enable bookings only for members (this will add a member restriction to all sale articles)',
    ),
    array(
        'key'         => 'cudi.print_collect_as_signin',
        'value'       => '0'
        'description' => 'Print collect ticket on sign-in instead of sign-in ticket.'
    ),
);
