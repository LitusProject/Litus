<?php

return array(
    array(
        'key'         => 'ticket.remove_reservation_treshold',
        'value'       => 'P2D',
        'description' => 'The date interval after which a person cannot remove a ticket reservation',
    ),
    array(
        'key'         => 'ticket.pdf_generator_path',
        'value'       => 'data/ticket/pdf_generator',
        'description' => 'The path to the PDF generator files',
    ),
    array(
        'key'         => 'ticket.upper_text',
        'value'       => 'I agree that this data will be used, following GDPR guidelines.',
        'description' => serialize(
            array(
                'en' => 'The text on the book tickets page',
                'nl' => 'The extra tekst op de tickets page'
            )
        )
    ),
    array(
        'key'         => 'ticket.confirmation_email_from',
        'value'       => 'tickets@vtk.be',
        'description' => 'Email address used for sending confirmation emails, also receives sent confirmation emails',
    ),
    array(
        'key'         => 'ticket.confirmation_email_body',
        'value'       => serialize(
            array(
                'subject' => 'VTK Tickets {{ event }}',
                'content' => 'Beste {{ fullname }},


U hebt tickets besteld voor {{ event }}.
Het gekozen ticket is:

{{ option }}

Indien u nog niet betaald hebt kan dit via volgende link: {{ paylink }}


Met Vriendelijke Groeten,

VTK



--- English ---

Dear {{ fullname }},


You ordered tickets for {{ event }}.
The chosen ticket is:

{{ option }}

Payment can be done through the following link should you not have paid yet: {{ paylink }}


Kind regards,

VTK'
            ),
        ),
        'description' => 'Email sent for confirmation of ticket reservation'
    ),
    array(
        'key'         => 'ticket.pay_link_domain',
        'value'       => 'vtk.be',
        'description' => 'The domain for the paylink used in generated emails',
    ),
);
