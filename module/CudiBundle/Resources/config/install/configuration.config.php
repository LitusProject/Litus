<?php

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
        'value'       => '/opt/fop/fop',
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
        'key'         => 'cudi.billing_address_VAT',
        'value'       => '',
        'description' => 'The VAT number of the billing organization of the cudi (empty means none)',
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

The opening hours of the Cudi can be found online on the homepage of the VTK website. These change weekly!
Also, be sure to reserve a time slot: no time slot = no books. You can find these time slots at https://vtk.be/nl/registration-shift/.

VTK Cudi

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Nieuwe Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties zijn aan u toegewezen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn online te vinden op de voorpagina van de VTK-site. Deze veranderen wekelijks!
Vergeet hierbij ook zeker geen tijdslot te reserveren: geen tijdslot = geen boeken. Deze tijdsloten kan je vinden op https://vtk.be/nl/registration-shift/

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a booking is assigned',
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

The opening hours of the Cudi can be found online on the homepage of the VTK website. These change weekly!
Also, be sure to reserve a time slot: no time slot = no books. You can find these time slots at https://vtk.be/nl/registration-shift/.

VTK Cudi

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Waarschuwing Vervallen Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties gaan binnekort vervallen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn online te vinden op de voorpagina van de VTK-site. Deze veranderen wekelijks!
Vergeet hierbij ook zeker geen tijdslot te reserveren: geen tijdslot = geen boeken. Deze tijdsloten kan je vinden op https://vtk.be/nl/registration-shift/

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a booking is about to expire',
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

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Vervallen Toewijzingen',
                    'content' => 'Beste,

De volgende reservaties zijn vervallen:
{{ bookings }}#expires#verviel op#expires#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent when a booking is expired',
    ),
    array(
        'key'         => 'cudi.queue_item_barcode_prefix',
        'value'       => '988000000000',
        'description' => 'The start for a serving queue item barcode',
    ),
    array(
        'key'         => 'cudi.queue_socket_file',
        'value'       => 'tcp://127.0.0.1:8899',
        'description' => 'The file used for the WebSocket of the queue',
    ),
    array(
        'key'         => 'cudi.queue_socket_public',
        'value'       => ':8899',
        'description' => 'The public address for the WebSocket of the queue',
    ),
    array(
        'key'         => 'cudi.queue_socket_key',
        'value'       => md5(uniqid(rand(), true)),
        'description' => 'The key used for the WebSocket of the queue',
    ),
    array(
        'key'         => 'cudi.queue_socket_enabled',
        'value'       => '1',
        'description' => 'Whether the cudi queue socket is enabled',
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
                'card'      => 'LITUS-Card',
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
                'F_S'  => 232,
                'F_M'  => 233,
                'F_L'  => 234,
                'F_XL' => 235,

                'M_S'  => 228,
                'M_M'  => 229,
                'M_L'  => 230,
                'M_XL' => 231,
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

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Catalogus Aanpassingen',
                    'content' => 'Beste,

De catalogus van onze cudi is aangepast:
{{ updates }}#bookable#is nu reserveerbaar#bookable# #unbookable#is niet meer reserveerbaar#unbookable# #added#is toegevoegd aan de catalogus#added# #removed#is verwijderd van de catalogus#removed#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The content of the mail send for catalog updates',
    ),
    array(
        'key'         => 'cudi.catalog_update_mail_enabled',
        'value'       => '1',
        'description' => 'Flag whether to send the catalog update mails.',
    ),
    array(
        'key'         => 'cudi.catalog_update_mail_to_sysadmin',
        'value'       => '1',
        'description' => 'Flag whether to send the catalog update mails tot sysadmin.',
    ),
    array(
        'key'         => 'cudi.sale_light_version',
        'value'       => '0',
        'description' => 'Flag whether to show the light version of the sale app (no queue)',
    ),
    array(
        'key'         => 'cudi.order_job_id',
        'value'       => '{{ date }}-VTK-{{ code }}',
        'description' => 'The job id for a XML exported order',
    ),
    array(
        'key'         => 'cudi.booking_mails_to_cudi',
        'value'       => '1',
        'description' => 'Send the cudi booking mails (assigned, expired, warning) to the cudi address',
    ),
    array(
        'key'         => 'cudi.booking_mails_to_sysadmin',
        'value'       => '1',
        'description' => 'Send the cudi booking mails (assigned, expired, warning) to the sytem administrator address',
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
        'value'       => '0',
        'description' => 'Print collect ticket on sign-in instead of sign-in ticket.',
    ),
    array(
        'key'         => 'cudi.signin_printer',
        'value'       => 'signin',
        'description' => 'Printer used to print sign-in tickets.',
    ),
    array(
        'key'         => 'cudi.card_printer',
        'value'       => 'card',
        'description' => 'Printer used to print membership cards.',
    ),
    array(
        'key'         => 'cudi.enable_sale_article_barcode_check',
        'value'       => '1',
        'description' => 'Enable the barcode checks for sale articles.',
    ),
    array(
        'key'         => 'cudi.enable_assign_after_stock_update',
        'value'       => '1',
        'description' => 'Enable assign after updating the stock (enable_automatic_assignment must also be enabled).',
    ),
    array(
        'key'         => 'cudi.export_order_format',
        'value'       => 'default',
        'description' => 'Select the order export xml format choose: default, pmr',
    ),
    array(
        'key'         => 'cudi.dissable_registration_articles_2nd_stock_period',
        'value'       => '0',
        'description' => 'Dissable the assignment of registration articles in the second semester.',
    ),
    array(
        'key'         => 'cudi.isic_enable_info',
        'value'       => '0',
        'description' => 'Enable the isic info on the bookings page.',
    ),
    array(
        'key'         => 'cudi.isic_service_url',
        'value'       => 'https://isicregistrations.guido.be/service.asmx',
        'description' => 'The url for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_username',
        'value'       => '',
        'description' => 'The username for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_password',
        'value'       => '',
        'description' => 'The password for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_client_id',
        'value'       => '',
        'description' => 'The client ID for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_student_cities',
        'value'       => serialize(
            array(
                'Leuven' => 'Leuven',
            )
        ),
        'description' => 'The student city for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_schools',
        'value'       => serialize(
            array(
                'KU Leuven' => 'KU Leuven',
            )
        ),
        'description' => 'The school for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_studies',
        'value'       => serialize(
            array(
                'Ingenieursstudies' => 'Engineering studies',
            )
        ),
        'description' => 'The studies for the ISIC SOAP API.',
    ),
    array(
        'key'         => 'cudi.isic_sale_article',
        'value'       => '0',
        'description' => 'The id of the ISIC sale article.',
    ),
    array(
        'key'         => 'cudi.isic_newsletter_mandatory',
        'value'       => '0',
        'description' => 'Whether or not the ISIC newsletter is mandatory.',
    ),
    array(
        'key'         => 'cudi.isic_partner_mandatory',
        'value'       => '0',
        'description' => 'Whether or not the ISIC partners information is mandatory.',
    ),
    array(
        'key'         => 'cudi.isic_delay_order',
        'value'       => '0',
        'description' => 'Whether or not to delay an ISIC order until it has been paid for.',
    ),
    array(
        'key'         => 'cudi.isic_Guido_conditions',
        'value'       => serialize(
            array(
                'en' => '<b>By selecting \'Receive information Guido NV\' I herby consent</b> with the storage of above data by GUIDO NV, Bruiloftstraat 127, 9050 Gentbrugge.<br>
This allows GUIDO to keep you updated in the future about their hints, tricks, news, activities, games etc...<br>
We will never sell your data to third parties.<br>
In each electronic communication you will receive from GUIDO, you will get the possibility to unsubscribe from future communications. You will also be possible to consult and correct your data at all times. (cfr. GDPR regulation to protect your privacy)
This is a direct link to our online-privacy page: <a href="https://www.guido.be/Specialepaginas/Privacy.aspx">https://www.guido.be/Specialepaginas/Privacy.aspx</a>.',
                'nl' => '<b>Door \'Stuur mij informatie van Guido NV\' te selecteren sta ik toe</b> dat bovenstaande gegevens kunnen worden opgenomen in het bestand van GUIDO NV, Bruiloftstraat 127, 9050 Gentbrugge.<br>
Zo kan GUIDO je ook in de toekomst op de hoogte houden van onze tips, trucs, nieuwtjes, activiteiten, wedstrijden etc...<br>
Wij verkopen je gegevens nooit aan derden.<br>
Bij elke elektronische post die je van GUIDO zal ontvangen, krijg je trouwens de mogelijkheid om je uit te schrijven voor toekomstige elektronische communicatie en je kunt je gegevens steeds raadplegen en laten verbeteren. (cfr. De GDPR-wetgeving op de privacy)<br>
Dit is de rechtstreekse link naar onze online-privacy pagina: <a href="https://www.guido.be/Specialepaginas/Privacy.aspx">https://www.guido.be/Specialepaginas/Privacy.aspx</a>.',
            )
        ),
        'description' => 'The additional conditions to be displayed below the isic registration form.',
    ),
    array(
        'key'         => 'cudi.show_mandatory_column',
        'value'       => '1',
        'description' => 'Whether or not to show the column obligatory.',
    ),
    array(
        'key'         => 'cudi.show_extra_text_reservation_page',
        'value'       => '0',
        'description' => 'Whether or not to show the text above the reservations.',
    ),
    array(
        'key'         => 'cudi.extra_text_reservation_page',
        'value'       => serialize(
            array(
                'en' => 'This is a placeholder text, please change me',
                'nl' => 'Deze tekst moet nog aanepast worden',
            )
        ),
        'description' => 'The additional displayed above the reservation overview.',
    ),
    array(
        'key'         => 'cudi.retail_maximal_relative_price',
        'value'       => '0.8',
        'description' => 'The maximal relative price to the sell price.',
    ),
    array(
        'key'         => 'cudi.retail_enquired_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'Enquiry for your retail: {{ book }}',
                    'content' => 'Dear,

You have received an enquiry for the following retail: {{ book }}.
Please contact {{ name }} via {{ email }}.

-- This is an automatically generated email, please do not reply --',
                ),
                'nl' => array(
                    'subject' => 'Interesse in je verkoop: {{ book }}',
                    'content' => 'Beste,

Iemand heeft interesse getoond voor het volgende boek: {{ book }}.
Gelieve contact op te nemen met {{ name }} via {{ email }}.

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
                ),
            )
        ),
        'description' => 'The mail sent to the owner of the retail when an enquiry is made.',
    ),
    array(
        'key'         => 'cudi.retail_allowed_types',
        'value'       => serialize(
            array(
                'slides',
                'textbook',
                'exercises',
            )
        ),
        'description' => 'An array of all allowed article types for retails.',
    ),
    array(
        'key'         => 'cudi.retail_overview_text',
        'value'       => serialize(
            array(
                'en' => 'Here you can sign up for the inter-student second-hand book store.',
                'nl' => 'Hier kan je je opgeven voor de tweedehandsbeurs',
            )
        ),
        'description' => 'The text on the overview page in cudi - retail',
    ),
    array(
        'key'                   => 'cudi.retail_my_deals_text',
        'value'                 => serialize(
            array(
                'en' => 'Here you can see your deals.',
                'nl' => 'Hier zie je je huidige aanvragen.',
            )
        ),        'description' => 'The text on the my_deals page in cudi - retail',
    ),
    array(
        'key'                   => 'cudi.retail_my_retails_text',
        'value'                 => serialize(
            array(
                'en' => 'Here you can see your personal retails, edit them or add new ones.',
                'nl' => 'Hier zie je je huidige aanbiedingen en kan je er nieuwe aanmaken.',
            )
        ),        'description' => 'The text on the my_retails page in cudi - retail',
    ),
    array(
        'key'         => 'cudi.retail_enabled',
        'value'       => '0',
        'description' => 'boolean for retail pages/buttons',
    ),
    array(
        'key'         => 'cudi.queue_force_registration_shift',
        'value'       => '0',
        'description' => 'boolean for forcing registration shifts',
    ),
    array(
        'key'         => 'cudi.queue_margin_sign_in',
        'value'       => '0',
        'description' => 'number of minutes someone can sign in to the queue before and after the registration shift',
    ),
    array(
        'key'         => 'cudi.printer_event_id',
        'value'       => 'here comes the id',
        'description' => 'This is the Ticket Event ID for the printer',
    ),
    array(
        'key'         => 'cudi.printer_uniflow_client_id',
        'value'       => 'Here comes the Uniflow Client ID',
        'description' => 'This is the Client ID for the uniflow aplication',
    ),
    array(
        'key'         => 'cudi.printer_uniflow_client_secret',
        'value'       => 'Here comes the uniflow Client Secret',
        'description' => 'This is the Client secret for the uniflow application',
    ),
    array(
        'key'         => 'cudi.maximum_booking_number',
        'value'       => '10',
        'description' => 'This is the maximum size of things someone can order in Cudi',
    ),
);
