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
        'key'         => 'br.invoice_expire_time',
        'value'       => 'P30D',
        'description' => 'The time interval within which a invoice must be paid. See http://www.php.net/manual/en/dateinterval.construct.php for the format.',
    ),
    array(
            'key'         => 'br.vat_number',
            'value'       => 'BE0479-482-282',
            'description' => 'The VAT number of the organisation sending the invoice.',
    ),
    array(
            'key'         => 'br.invoice_vat_explanation',
            'value'       => 'CHANGE ME I should explain VAT',
            'description' => 'Explains what the VAT',
    ),
    array(
        'key'         => 'br.invoice_below_entries',
        'value'       => serialize(
            array(
                'en' => 'Please pay at the latest 30 days after the invoice date, as agreed in the contract. For information of a financial nature, you can always contact beheer@vtk.be.',
                'nl' => 'Gelieve het bovenstaande factuurbedrag te betalen ten laatste 30 dagen na factuurdatum, zoals overeengekomen in het contract. Voor inlichtingen van financiële aard kan u steeds terecht bij beheer@vtk.be .',
            )
        ),
        'description' => 'Payment information',
    ),
    array(
            'key'         => 'br.contract_language',
            'value'       => 'en',
            'description' => 'The language the contracts are in',
    ),
    array(
        'key'         => 'br.account_activated_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'VTK Corporate Account',
                    'content' => 'Dear {{ name }},

A corporate account was created for you with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}
You can use this account to view the CV Book at http://litus/corporate

Kind regards,

The VTK Corporate Relations Team',
                ),
                'nl' => array(
                    'subject' => 'VTK Bedrijfsaccount',
                    'content' => 'Beste {{ name }},

Een bedrijfsaccount werd voor u aangemaakt met gebruikersnaam {{ username }}.
Klik hier om deze te activeren: http://litus/account/activate/code/{{ code }}
U kan dit account gebruiken om het CV Book te bekijken op http://litus/corporate

Met vriendelijke groeten,

Het VTK Bedrijvenrelaties Team',
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
            'key'         => 'br.contract_name',
            'value'       => 'Vlaamse Technische Kring',
            'description' => 'I have no idea',
    ),
    array(
            'key'         => 'br.contract_final_entry',
            'value'       => 'Will see where I show up contract_final_entry!!',
            'description' => 'I have no idea contract_final_entry',
    ),
    array(
        'key'         => 'br.contract_below_entries',
        'value'       => serialize(
            array(
                'en' => 'Herewith I agree to the terms and conditions of VTK Leuven, found at the end of this contract.',
                'nl' => 'Hiermede ga ik akkoord met de algemene verkoopsvoorwaarden van VTK Leuven, te vinden aan het einde van dit contract.',
            )
        ),
        'description' => 'Payment information',
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
            'key'         => 'br.organization_name',
            'value'       => 'VTK Ondersteuning vzw',
            'description' => 'Name of corporate relations vzw',
    ),
    array(
            'key'         => 'br.invoice_number',
            'value'       => '1',
            'description' => 'The default number that is used on an invoice created by BR',
    ),
    array(
        'key'         => 'br.vat_types',
        'value'       => serialize(
            array(
                6,
                11,
                21,
            )
        ),
        'description' => 'The possible amounts of VAT',
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
        'description' => 'The cv archive years',
    ),
    array(
            'key'         => 'br.product_contract_text',
            'value'       => '* You have to start every line with a *.
* Also the second bullet.
  * You can also make a sub bullet only use spaces before the bullet. It must be lined up with the text above.
  * The next sub bullet most be lined up with the one above.
  At a sublevel you can also drop the bullet. This will be renderered without bullet.',
            'description' => 'The standard text that is displayed on the creation of a new product.',
    ),
);
