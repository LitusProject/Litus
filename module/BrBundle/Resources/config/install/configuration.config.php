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
        'key'         => 'br.file_path',
        'value'       => 'data/br/files',
        'description' => 'The path to the company files',
    ),
    array(
        'key'         => 'br.public_logo_path',
        'value'       => '_br/img',
        'description' => 'The path to the public company logo files',
    ),
    array(
        'key'         => 'br.pdf_generator_path',
        'value'       => 'data/br/pdf_generator',
        'description' => 'The path to the PDF generator files',
    ),
    array(
        'key'         => 'br.cv_book_open',
        'value'       => '0',
        'description' => 'Whether the CV Book is currently open for entries or not',
    ),
    array(
        'key'         => 'br.account_activated_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'VTK Corporate Account',
                    'content' => 'Dear {{ name }},

A corporate account for you was created on VTK with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}
You can use this account to view the CV Book at http://litus/corporate

With best regards,
The VTK Corporate Team'
                ),
                'nl' => array(
                    'subject' => 'VTK Bedrijven Account',
                    'content' => 'Beste {{ name }},

Een bedrijven account was voor u aangemaakt op VTK met gebruikersnaam{{ username }}.
Klik hier om deze te activeren: http://litus/account/activate/code/{{ code }}
U kan dit account gebruiken om het CV Book te bekijken op http://litus/corporate

Met vriendelijke groeten,
Het VTK Bedrijvenrelaties Team'
                ),
            )
        ),
        'description' => 'The email sent when an account is activated',
    ),
    array(
        'key'         => 'br.cv_book_language',
        'value'       => 'nl',
        'description' => 'The language used in the printed version of the CV Book',
    ),
    array(
        'key'         => 'br.cv_book_foreword',
        'value'       => '<section title="Example section">
<content>
Example content of this section.
</content>
</section>',
        'description' => 'The foreword of the CV Book',
    ),
    array(
        'key'         => 'br.vat_types',
        'value'       => serialize(
            array(
                6,
                11,
                21
            )
        ),
        'description' => 'The possible amounts of VAT'
    ),
    array(
        'key'         => 'br.cv_archive_years',
        'value'       => serialize(
            array(
                /*
                '0910' => array(
                    'full_year' => '2009-2010',
                    'file' => 'cvboek0910.pdf',
                ),
                 */
            )
        ),
        'description' => 'The cv archive years'
    )
);
