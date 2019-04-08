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
        'key'   => 'prom.confirmation_mail',
        'value' => serialize(
            array(
                'from'    => 'galabal@vtk.be',
                'subject' => 'VTK Galabal Busreservatie - VTK Prom Bus Reservation ',
                'body'    => 'Beste,
[English below]

Uw reservatie voor de bus op {{ busTime }} is succesvol opgeslagen.

Indien u graag uw busreservatie aanpast kan dit gedaan worden op volgende link : vtk.be/prom/registration/ .

Alvast tot dan!

- - - - - - - - - - - -

Your bus reservation on {{ busTime }} has succesfully been registered.

If you want to edit your registration, you can always follow the next link : vtk.be/prom/registration/ .

See you then!

-- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
-- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'The prom bus confirmation mail ',
    ),
    array(
        'key'         => 'prom.enable_reservations',
        'value'       => 0,
        'description' => 'Enable busreservations',
    ),
    array(
        'key'   => 'prom.remove_mail',
        'value' => serialize(
            array(
                'from'    => 'galabal@vtk.be',
                'subject' => 'VTK Galabal Busreservatie - VTK Prom Bus Reservation ',
                'body'    => 'Beste,
[English below]

Uw reservatie voor de bus op {{ busTime }} is verwijderd.

Indien u graag een nieuwe busreservatie maakt, kan dit gedaan worden op volgende link : vtk.be/prom/registration/ .

Bij problemen kan u altijd een mail sturen naar it@vtk.be .

Alvast tot dan!

- - - - - - - - - - - -

Your bus reservation on {{ busTime }} has been removed.

If you want to create a new registration, you can always follow the next link : vtk.be/prom/registration/ .

In case of problems, feel free to mail it@vtk.be .

See you then!

-- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
-- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'The prom bus confirmation mail ',
    ),
    array(
        'key'   => 'prom.reservation_mail',
        'value' => serialize(
            array(
                'from'    => 'buscodes@vtk.be',
                'subject' => 'VTK Galabal Unieke code Busreservatie - VTK Prom Unique Bus Reservation Code',
                'body'    => 'Beste,
    [English below]

    Bedankt voor uw inschrijving voor het VTK Galabal.
    Gelieve uw onderstaande unieke code goed bij te houden, u zal ze nodig hebben om een plaats op de bussen te reserveren via vtk.be/prom/registration.
    De opening van de registraties zal bekend gemaakt worden op de Facebook-pagina van VTK.

    {{ reservationCode }} .

    Alvast tot dan!

    - - - - - - - - - - - -

    Thank you for signing up for the VTK prom.
    Please do not lose your unique code below, as you will need it to book a spot on the buses through www.vtk.be/prom/registration.
    The exact date and time for this will be announced later on the VTK Facebook page.

    {{ reservationCode }}

    See you then!

    -- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
    -- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'The prom reservation code mail.',
    ),
    array(
        'key'   => 'prom.reservation_opening_warning',
        'value' => serialize(
            array(
                'from'    => 'buscodes@vtk.be',
                'subject' => 'Busreservaties VTK Galabal geopend - VTK Prom Bus reservations have opened!',
                'body'    => 'Beste,
    [English below]

    Busreservaties zijn vanaf nu mogelijk via vtk.be/prom/registration.
    U zal uw unique buscode nodig hebben die eerder is doorgestuurd per mail. Gelieve ook zeker uw spam na te kijken indien u denkt dat u deze niet ontvangen hebt.

    - - - - - - - - - - - -

    The registrations for the bus have opened. You can now book a spot on the buses through vtk.be/prom/registration.
    You will need your unique bus code which has been sent to you earlier by mail. Be sure to check your spam if you think you have not received your unique code(s).

    See you then!

    -- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
    -- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'Warning mail for the opening of the bus registrations.',
    ),
);
