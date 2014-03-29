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
        'key'         => 'form.file_upload_path',
        'value'       => 'data/form/files',
        'description' => 'The path to the uploaded form files',
    ),
    array(
        'key'         => 'form.mail_confirmation',
        'value'       => serialize(
            array(
                'en' => array(
                    'content' => 'Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
VTK',
                ),
                'nl' => array(
                    'content' => 'Beste %first_name% %last_name%,

Uw inschrijving was succesvol. Uw unieke inschrijving id is %id%. Hieronder is een overzicht van de ingevulde waarden:

%entry_summary%

Met vriendelijke groeten,
VTK',
                ),
            )
        ),
        'description' => 'The mail template for confirmation mails',
    ),
    array(
        'key'         => 'form.mail_reminder',
        'value'       => serialize(
            array(
                'en' => array(
                    'content' => 'Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
VTK',
                ),
                'nl' => array(
                    'content' => 'Beste %first_name% %last_name%,

Uw inschrijving was succesvol. Uw unieke inschrijving id is %id%. Hieronder is een overzicht van de ingevulde waarden:

%entry_summary%

Met vriendelijke groeten,
VTK',
                ),
            )
        ),
        'description' => 'The mail template for confirmation mails',
    ),
);
