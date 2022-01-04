<?php

return array(
    array(
        'key'         => 'br.file_path',
        'value'       => 'data/br/files',
        'description' => 'The path to the company files',
    ),
    array(
        'key'         => 'br.br_group_coordinator',
        'value'       => 'Evert Cuylen',
        'description' => 'The name of the current group coordinator, used for generating contracts.',
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
        'key'         => 'br.vat_number',
        'value'       => 'BE0479-482-282',
        'description' => 'The VAT number of the organisation sending the invoice.',
    ),
    array(
        'key'         => 'br.invoice_vat_explanation',
        'value'       => serialize(
            array(
                'eu'     => 'Vul in VAT voor EU companies',
                'non-eu' => 'Vul dit in VAT voor non-EU companies'
            )
        ),
        'description' => 'Explains what the VAT',
    ),
    array(
        'key'         => 'br.invoice_below_entries',
        'value'       => serialize(
            array(
                'en' => 'Please pay at the latest <payment_days/> days after the invoice date, as agreed in the contract. For information of a financial nature, you can always contact beheer@vtk.be.',
                'nl' => 'Gelieve het bovenstaande factuurbedrag te betalen ten laatste <payment_days/> dagen na factuurdatum, zoals overeengekomen in het contract. Voor inlichtingen van financiële aard kan u steeds terecht bij beheer@vtk.be .',
            )
        ),
        'description' => 'Payment information',
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
        'key'         => 'br.contract_above_signatures',
        'value'       => serialize(
            array(
                'en' => 'Contract drawn up in in duplicate at ',
                'nl' => 'Contract opgemaakt in tweevoud te ',
            )
        ),
        'description' => 'Extra line contract first part',
    ),
    array(
        'key'         => 'br.contract_above_signatures_middle',
        'value'       => serialize(
            array(
                'en' => ' on ',
                'nl' => ' op ',
            )
        ),
        'description' => 'Extra line contract second part',
    ),
    array(
        'key'         => 'br.corporate_jobfair_info',
        'value'       => 'Dear participant of the Student IT Jobfair,

To complete your participation at our Jobfair, we kindly request to fill out the forms below so that we can provide you with optimal service. Not all forms have to be filled out at once as you can can continue your process where you left off. Every form has its own deadline though:<br><br>



If you have any more requests, questions or encounter issues filling out this form, please contact us at corporaterelations@vtk.be .<br><br>

Student IT Corporate Relations wishes you a successful recruiting event!<br><br>

Student IT Corporate Relations',
        'description' => 'Information on corporate jobfair overview',
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
        'key'         => 'br.invoice_book_number',
        'value'       => '1',
        'description' => 'The default number that is used on an invoice created by BR',
    ),
    array(
        'key'         => 'br.invoice_year_number',
        'value'       => '2017',
        'description' => 'The default year prefix used for invoice numbers',
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
                Structure:
                {key: year code XXYY} => array:
                    {key: 'full_year'} => {full year XXXX-YYYY}
                    {key: 'file'}      => {filename}
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
    array(
        'key'         => 'br.contract_number_codes',
        'value'       => serialize(
            array(
                '1415' => '22',
                '1516' => '33',
                '1617' => '44',
                '1718' => '55',
            )
        ),
        'description' => 'The codes to use for contract number generation.',
    ),
    array(
        'key'         => 'br.automatic_discounts',
        'value'       => serialize(
            array(
                '400000' => '5',
            )
        ),
        'description' => 'The automatic discount: (money value (in cents, discount percentage)',
    ),
    array(
        'key'         => 'br.contract_payment_details',
        'value'       => serialize(
            array(
                'nl' => '* Het Bedrijf verklaart de volgende betalingsvoorwaarden na te leven: <total_price/> te betalen uiterlijk <payment_days/> dagen na facturatiedatum. De storting dient te gebeuren op het rekeningnummer BE30 7450 1759 0011 van VTK Ondersteuning vzw met vermelding van het factuurnummer.',
                'en' => '* The company will comply to the following terms of payment: <total_price/> to be paid <payment_days/> days after the invoice date. The payment will need to happen in the account of VTK Ondersteuning VZW with IBAN BE30 7450 1759 0011 stating the invoice number.',
            )
        ),
        'description' => 'The standard payment details text that is displayed on the creation of a new contract.',
    ),
    array(
        'key'         => 'br.contract_auto_discount_text',
        'value'       => serialize(
            array(
                'nl' => '* Vanwege de trouwe samenwerking biedt VTK een partnership aan. Dit houdt in dat het logo van het bedrijf gratis in ons wekelijks boekje \'t Bakske en op de homepage van de website www.vtk.be te zien zal zijn. Zo staat het bedrijf het hele jaar door in de spotlights. Bovendien krijgt het bedrijf een korting van 5% op het totale bedrag van dit contract.',
                'en' => '* ?'
            )
        ),
        'description' => 'The standard auto discount text that is displayed on the creation of a new contract.',
    ),
    array(
        'key'         => 'br.invoice_auto_discount_text',
        'value'       => 'Partnership: logo op www.vtk.be, wekelijks in \'t Bakske, 5% korting',
        'description' => 'The standard auto discount text that is displayed on the creation of a new invoice.',
    ),
    array(
        'key'         => 'br.vacancy_mail',
        'value'       => 'bedrijvenrelaties@vtk.be',
        'description' => 'The mail address to which notifications should be send when a company makes a new vacancy/internship request.',
    ),
    array(
        'key'         => 'br.vacancy_mail_name',
        'value'       => 'VTK Bedrijvenrelaties',
        'description' => '',
    ),
    array(
        'key'         => 'br.vacancy_link',
        'value'       => 'https://vtk.be/admin/br/request/',
        'description' => '',
    ),
    array(
        'key'         => 'br.student_job_mail',
        'value'       => 'bedrijvenrelaties@vtk.be',
        'description' => 'The mail address to which notifications should be send when a company makes a new vacancy/StudentJob request.',
    ),
    array(
        'key'         => 'br.student_job_mail_name',
        'value'       => 'VTK Bedrijvenrelaties',
        'description' => '',
    ),
    array(
        'key'         => 'br.student_job_link',
        'value'       => 'https://vtk.be/admin/br/request/',
        'description' => '',
    ),
    array(
        'key'         => 'br.invoice_header_extra_text',
        'value'       => 'RPR Leuven',
        'description' => '',
    ),
    array(
        'key'         => 'br.career_page_text',
        'value'       => serialize(
            array(
                'nl' => array(
                    'br_career_student_launch' => array(
                        'main' => 'VTK als accelerator van jouw carrière',
                        'sub'  => 'Ontdek wat wij voor jou als toekomstig ingenieur betekenen',
                    ),
                    'br_career_student_container' => array(
                        'main' => 'VTK maakt voor jou het vershil',
                        'sub'  => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                    'br_career_student_search' => array(
                        'search' => 'Op zoek naar een stage, job of studentenjob?',
                    ),
                ),
                'en' => array(
                    'br_career_student_launch' => array(
                        'main' => 'VTK as accelerator for your future',
                        'sub'  => 'Discover what we can do for your future',
                    ),
                    'br_career_student_container' => array(
                        'main' => 'VTK makes the difference',
                        'sub'  => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                    'br_career_student_search' => array(
                        'search' => 'Searching for an internship, job or student job?',
                    ),
                ),
            )
        ),
        'description' => 'The texts for the career page',
    ),
    array(
        'key'         => 'br.corporate_page_text',
        'value'       => serialize(
            array(
                'nl' => array(
                    'br_corporate_launch' => array(
                        'main' => 'VTK als partner voor jouw bedrijf',
                        'sub'  => 'Kom te weten wat wij voor jou kunnen betekenen',
                    ),
                    'br_corporate_intro' => array(
                        'main' => 'VTK maakt voor jou het vershil',
                        'sub'  => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                    'br_corporate_events' => array(
                        'main' => 'Zien we jou op één van onze volgende evenementen?',
                        'sub'  => 'Evenementen bekijken',
                    ),
                    'br_corporate_services' => array(
                        'title' => 'Hoe wij u bedrijf mee boosten',
                        'one'   => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                        'two'   => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                        'three' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                ),
                'en' => array(
                    'br_corporate_launch' => array(
                        'main' => 'VTK as partner for your company',
                        'sub'  => 'Get to know what we can do for you',
                    ),
                    'br_corporate_intro' => array(
                        'main' => 'VTK makes the difference for you',
                        'sub'  => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                    'br_corporate_events' => array(
                        'main' => 'Do we see you on one of our next events?',
                        'sub'  => 'See events',
                    ),
                    'br_corporate_services' => array(
                        'title' => 'How we help to boost your company',
                        'one'   => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                        'two'   => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                        'three' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.',
                    ),
                ),
            ),
        ),
        'description' => 'The texts for the corporate page'
    ),
    array(
        'key'         => 'br.communication_options',
        'value'       => serialize(
            array(
                'Mail',
                'Facebook',
                'Instagram',
                'Bakske',
                'IrReeel',
            )
        ),
        'description' => 'The options displayed in the communications tab in the br admin',
    ),
    array(
        'key'         => 'br.communication_mail',
        'value'       => 'stan.cardinaels@vtk.be',
        'description' => 'The mail address to which notifications should be send when a duplicate communication date is chosen.',
    ),
    array(
        'key'         => 'br.communication_mail_name',
        'value'       => 'VTK Bedrijvenrelaties',
        'description' => '',
    ),
    array(
        'key'         => 'br.communication_mail_body',
        'value'       => serialize(
            array(
                'subject' => 'Dubbele boeking voor communicatie op {{ date }}',
                'content' => 'Beste
Er is een dubbele communicatie boeking aangemaakt op {{ date }} door {{ person }}.
Communicatie optie: {{ option }}.
Nieuw Doelpubliek: {{ newAudience }}.
Nieuw Bedrijf: {{ newCompany }}.
Bestaand Doelpubliek: {{ oldAudience }}.
Bestaand Bedrijf: {{ oldCompany }}.
-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --',
            )
        ),
        'description' => 'The mail sent when a duplicate date is chosen for a communication'
    ),
    array(
        'key'         => 'br.cv_grades_map_enabled',
        'value'       => 0,
        'description' => 'Use grades or the grades Map',
    ),
    array(
        'key'         => 'br.cv_grades_map',
        'value'       => serialize(
            array(
                6800  =>  "Cum fructu",
                7700  =>  "Cum laude",
                8500  =>  "Magna cum laude",
                9000  =>  "Summa cum laude",
                10000  =>  "Summa cum laude with the congratulations of the examination committee",
            )
        ),
        'description' => '',
    ),
);
