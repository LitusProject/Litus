<?php

return array(
    array(
        'key'         => 'form.file_upload_path',
        'value'       => 'data/form/files',
        'description' => 'The path to the uploaded form files',
    ),
    array(
        'key'   => 'form.mail_confirmation',
        'value' => serialize(
            array(
                'en' => array(
                    'content' => 'Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

#guest_login_text#You can view and edit (if allowed) your subscription here:#guest_login_text#%guest_login%

With best regards,
VTK',
                ),
                'nl' => array(
                    'content' => 'Beste %first_name% %last_name%,

Uw inschrijving was succesvol. Uw unieke inschrijving id is %id%. Hieronder is een overzicht van de ingevulde waarden:

%entry_summary%

#guest_login_text#Je kan je inschrijving hier bekijken en bewerken (indien toegestaan):#guest_login_text#%guest_login%

Met vriendelijke groeten,
VTK',
                ),
            )
        ),
        'description' => 'The mail template for confirmation mails',
    ),
    array(
        'key'   => 'form.mail_reminder',
        'value' => serialize(
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
