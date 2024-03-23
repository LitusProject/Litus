<?php

return array(
    array(
        'key'         => 'shop.name',
        'value'       => 'Theokot',
        'description' => 'The name of the shop',
    ),
    array(
        'key'         => 'shop.mail',
        'value'       => 'theokot@vtk.be',
        'description' => 'The email address of the shop',
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
        'key'         => 'shop.no-reply_mail',
        'value'       => 'no-reply-theokot@vtk.be',
        'description' => 'The shop no-reply email address',
    ),
    array(
        'key'         => 'shop.no-reply_mail_name',
        'value'       => 'VTK Theokot',
        'description' => 'The signature name for shop no-reply mails',
    ),
    array(
        'key'         => 'shop.no_show_config',
        'value'       => serialize(
            array(
                '0' => array(
                    'mail_subject' => 'VTK Theokot Warning',
                    'mail_content' => '(English version below)

Dag {{ name }}

Je hebt onlangs een broodje en/of slaatje besteld in het Theokot en kwam dit niet ophalen binnen de aangeduide uren. Hierdoor moeten we op het einde van de dag jouw broodje gratis weggeven, of nog erger, weggooien. Bovendien werd dit item speciaal voor jou gereserveerd, waardoor andere studenten de kans op een lekker broodje mislopen.

Bij deze ontvang je een mail als waarschuwing: als dit nog een keer gebeurt, verlies je het privilege om een broodje en/of slaatje in het Theokot te bestellen gedurende 1 week.

Noot: we snappen volledig dat het kan gebeuren dat je onverwachts je bestelling niet kan afhalen. We stellen één van volgende oplossingen voor:
- Geef je r-nummer door aan je vrienden, die dan zelf het broodje en/of slaatje komen afhalen.
- Verwijder je reservatie voor 11u.

Het team hoopt op je begrip.
Groentjes, het Theokotteam

ENGLISH VERSION

Dear {{ name }},

You recently placed an order for a sandwich and/or salad at Theokot and did not pick it up within the specified hours. As a result, we are forced to either give away your sandwich for free at the end of the day or, even worse, discard it. Furthermore, this item was specifically reserved for you, potentially depriving other students of the opportunity to enjoy a delicious sandwich.

We are sending this email as a warning: if this happens again, you will lose the privilege of ordering a sandwich and/or salad at Theokot for a period of 1 week.

Please note that we fully understand that unexpected circumstances may prevent you from collecting your order. We offer one of the following solutions:
- Share your R-number with friends so that they can collect the sandwich and/or salad on your behalf.
- Delete your reservation before 11am.

The team hopes for your understanding.
Best regards,
The Theokot Team',
                    'ban_days'     => '0 days',
                ),
                '1' => array(
                    'mail_subject' => 'VTK Theokot Warning',
                    'mail_content' => '(English version below)

Dag {{ name }}

Je hebt onlangs een broodje en/of slaatje besteld in het Theokot en kwam dit niet ophalen binnen de aangeduide uren. Hierdoor moeten we op het einde van de dag jouw broodje gratis weggeven, of nog erger, weggooien. Bovendien werd dit item speciaal voor jou gereserveerd, waardoor andere studenten de kans op een lekker broodje mislopen.

Omdat dit al vaker is gebeurd, verlies je het privilege om een broodje/slaatje te bestellen voor 1 week. 
Je bent uiteraard altijd nog welkom om croques, worstenbroodjes, drankjes... te kopen. 

Noot: we snappen volledig dat het kan gebeuren dat je onverwachts je bestelling niet kan afhalen. We stellen één van volgende oplossingen voor:
- Geef je r-nummer door aan je vrienden, die dan zelf het broodje en/of slaatje komen afhalen.
- Verwijder je reservatie voor 11u.

Het team hoopt op je begrip.
Groentjes, het Theokotteam

ENGLISH VERSION

Dear {{ name }}

You recently placed an order for a sandwich and/or salad at Theokot and did not pick it up within the specified hours. As a result, we are forced to either give away your sandwich for free at the end of the day or, even worse, discard it. Furthermore, this item was specifically reserved for you, potentially depriving other students of the opportunity to enjoy a delicious sandwich.

Because this has happened before, you will lose the privilege of ordering a sandwich/salad for 1 week. However, you are still welcome to purchase croques, sausage rolls, beverages, and more.

Please note that we fully understand that unexpected circumstances may prevent you from collecting your order. We offer one of the following solutions:
- Share your R-number with friends so that they can collect the sandwich and/or salad on your behalf.
- Delete your reservation before 11am.

The team hopes for your understanding.
Best regards,
The Theokot Team',
                    'ban_days'     => '7 days',
                ),
                '2' => array(
                    'mail_subject' => 'VTK Theokot Warning',
                    'mail_content' => '(English version below)

Dag {{ name }}

Je hebt onlangs een broodje en/of slaatje besteld in het Theokot en kwam dit niet ophalen binnen de aangeduide uren. Hierdoor moeten we op het einde van de dag jouw broodje gratis weggeven, of nog erger, weggooien. Bovendien werd dit item speciaal voor jou gereserveerd, waardoor andere studenten de kans op een lekker broodje mislopen.

Omdat dit al vaker is gebeurd, verlies je het privilege om een broodje/slaatje te bestellen voor 1 week. 
Je bent uiteraard altijd nog welkom om croques, worstenbroodjes, drankjes... te kopen. 

Noot: we snappen volledig dat het kan gebeuren dat je onverwachts je bestelling niet kan afhalen. We stellen één van volgende oplossingen voor:
- Geef je r-nummer door aan je vrienden, die dan zelf het broodje en/of slaatje komen afhalen.
- Verwijder je reservatie voor 11u.

Het team hoopt op je begrip.
Groentjes, het Theokotteam

ENGLISH VERSION

Dear {{ name }}

You recently placed an order for a sandwich and/or salad at Theokot and did not pick it up within the specified hours. As a result, we are forced to either give away your sandwich for free at the end of the day or, even worse, discard it. Furthermore, this item was specifically reserved for you, potentially depriving other students of the opportunity to enjoy a delicious sandwich.

Because this has happened before, you will lose the privilege of ordering a sandwich/salad for 1 week. However, you are still welcome to purchase croques, sausage rolls, beverages, and more.

Please note that we fully understand that unexpected circumstances may prevent you from collecting your order. We offer one of the following solutions:
- Share your R-number with friends so that they can collect the sandwich and/or salad on your behalf.
- Delete your reservation before 11am.

The team hopes for your understanding.
Best regards,
The Theokot Team',
                    'ban_days'     => '7 days',
                ),
                'default' => array(
                    'mail_subject' => 'VTK Theokot Warning',
                    'mail_content' => '(English version below)

Dag {{ name }}

Je hebt onlangs een broodje en/of slaatje besteld in het Theokot en kwam dit niet ophalen binnen de aangeduide uren. Hierdoor moeten we op het einde van de dag jouw broodje gratis weggeven, of nog erger, weggooien. Bovendien werd dit item speciaal voor jou gereserveerd, waardoor andere studenten de kans op een lekker broodje mislopen.

Omdat dit al vaker is gebeurd, verlies je het privilege om een broodje/slaatje te bestellen voor {{ ban_weeks }} weken. 
Je bent uiteraard altijd nog welkom om croques, worstenbroodjes, drankjes... te kopen. 

Noot: we snappen volledig dat het kan gebeuren dat je onverwachts je bestelling niet kan afhalen. We stellen één van volgende oplossingen voor:
- Geef je r-nummer door aan je vrienden, die dan zelf het broodje en/of slaatje komen afhalen.
- Verwijder je reservatie voor 11u.

Het team hoopt op je begrip.
Groentjes, het Theokotteam

ENGLISH VERSION

Dear {{ name }}

You recently placed an order for a sandwich and/or salad at Theokot and did not pick it up within the specified hours. As a result, we are forced to either give away your sandwich for free at the end of the day or, even worse, discard it. Furthermore, this item was specifically reserved for you, potentially depriving other students of the opportunity to enjoy a delicious sandwich.

Because this has happened before, you will lose the privilege of ordering a sandwich/salad for {{ ban_weeks }} weeks. However, you are still welcome to purchase croques, sausage rolls, beverages, and more.

Please note that we fully understand that unexpected circumstances may prevent you from collecting your order. We offer one of the following solutions:
- Share your R-number with friends so that they can collect the sandwich and/or salad on your behalf.
- Delete your reservation before 11am.

The team hopes for your understanding.
Best regards,
The Theokot Team',
                    'ban_days'     => '{{ ban_weeks }}',
                ),
            ),
        ),
        'description' => 'Holds a no-show warning email and a amount of ban days for each amount of warnings the user has',
    ),
);
