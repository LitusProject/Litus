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
        'key'         => 'prom.confirmation_mail',
        'value'       => serialize(
            array(
                'from'         => 'galabal@vtk.be',
                'subject'   => 'VTK Galabal Busreservatie - VTK Prom Bus Reservation ',
                'body'    => 'Beste,
[English below]

Uw reservatie voor de bus op {{ busTime }} is succesvol opgeslagen.

Indien u graag uw busreservatie aanpast kan dit gedaan worden op volgende link : vtk.be/buses/reservation .

Alvast tot dan!

- - - - - - - - - - - -

Your bus reservation on {{ busTime }} has succesfully been registered.

If you want to edit your registration, you can always follow the next link : vtk.be/buses/reservation .

See you then!

-- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
-- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'The prom bus confirmation mail ',
    ),

    array(
        'key'         => 'prom.remove_mail',
        'value'       => serialize(
            array(
                'from'         => 'galabal@vtk.be',
                'subject'   => 'VTK Galabal Busreservatie - VTK Prom Bus Reservation ',
                'body'    => 'Beste,
[English below]

Uw reservatie voor de bus op {{ busTime }} is verwijderd.

Indien u graag een nieuwe busreservatie maakt, kan dit gedaan worden op volgende link : vtk.be/buses/reservation .

Bij problemen kan u altijd een mail sturen naar it@vtk.be .

Alvast tot dan!

- - - - - - - - - - - -

Your bus reservation on {{ busTime }} has been removed.

If you want to create a new registration, you can always follow the next link : vtk.be/buses/reservation .

In case of problems, feel free to mail it@vtk.be .

See you then!

-- Dit is een automatisch gegenereerde email, gelieve hier niet op te antwoorden --
-- This is an automatically generated email, please do not reply --',
            )
        ),
        'description' => 'The prom bus confirmation mail ',
    ),
);
