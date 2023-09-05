<?php

return array(
    array(
        'key'         => 'common.profile_path',
        'value'       => '/_common/profile',
        'description' => 'The path for profile photo\'s',
    ),
    array(
        'key'         => 'search_max_results',
        'value'       => '30',
        'description' => 'The maximum number of search results shown',
    ),
    array(
        'key'         => 'common.account_deactivated_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'VTK Account Deactivated',
                    'content' => 'Dear {{ name }},

Your account has been deactivated.
Click here to activate it again: http://litus/account/activate/code/{{ code }}

Kind regards,

VTK',
                ),
                'nl' => array(
                    'subject' => 'VTK Account Gedeactiveerd',
                    'content' => 'Beste {{ name }},

Uw account werd gedeactiveerd.
Klik hier om deze opnieuw te activeren: http://litus/account/activate/code/{{ code }}

Met vriendelijke groeten,

VTK',
                ),
            )
        ),
        'description' => 'The email sent when an account is deactivated',
    ),
    array(
        'key'         => 'common.account_activated_mail',
        'value'       => serialize(
            array(
                'en' => array(
                    'subject' => 'VTK Account Activated',
                    'content' => 'Dear {{ name }},

An account was created for you with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}

Kind regards,

VTK',
                ),
                'nl' => array(
                    'subject' => 'VTK Account Geactiveerd',
                    'content' => 'Beste {{ name }},

Een account werd voor u aangemaakt met gebruikersnaam {{ username }}.
Klik hier om deze te activeren: http://litus/account/activate/code/{{ code }}

Met vriendelijke groeten,

VTK',
                ),
            )
        ),
        'description' => 'The email sent when an account is activated',
    ),
    array(
        'key'         => 'system_mail_address',
        'value'       => 'vtk@vtk.be',
        'description' => 'The system mail address',
    ),
    array(
        'key'         => 'system_mail_name',
        'value'       => 'VTK',
        'description' => 'The system mail name',
    ),
    array(
        'key'         => 'common.mail_domain',
        'value'       => 'vtk.be',
        'description' => 'mail domain used by unit csv generator',
    ),
    array(
        'key'         => 'organization_short_name',
        'value'       => 'VTK',
        'description' => 'The short name of this organization',
    ),
    array(
        'key'         => 'organization_name',
        'value'       => 'Vlaamse Technische Kring',
        'description' => 'The full name of this organization',
    ),
    array(
        'key'         => 'organization_logo',
        'value'       => 'data/images/logo/logo.svg',
        'description' => 'The path to the logo of the organization',
    ),
    array(
        'key'         => 'organization_address_array',
        'value'       => serialize(
            array(
                'street'  => 'Studentenwijk Arenberg',
                'number'  => '6',
                'mailbox' => '1',
                'postal'  => '3001',
                'city'    => 'Heverlee',
                'country' => 'Belgium',
            )
        ),
        'description' => 'The address of the organization',
    ),
    array(
        'key'         => 'organization_url',
        'value'       => 'http://vtk.be',
        'description' => 'The URL of the organization',
    ),
    array(
        'key'         => 'enable_organization_signature',
        'value'       => '0',
        'description' => 'The signature of the organization',
    ),
    array(
        'key'         => 'university',
        'value'       => 'KU Leuven',
        'description' => 'The name of the university',
    ),
    array(
        'key'         => 'faculty',
        'value'       => 'Faculty of Engineering',
        'description' => 'The name of the faculty',
    ),
    array(
        'key'         => 'start_organization_year',
        'value'       => '{{ year }}-7-15 0:0:0',
        'description' => 'The start date of the organization year',
    ),
    array(
        'key'         => 'start_academic_year_offset',
        'value'       => 'P1W',
        'description' => 'The date interval the academic year will start before the official start',
    ),
    array(
        'key'         => 'shibboleth_url',
        'value'       => 'https://faye.vtk.be:8443/Shibboleth.sso/Login?target=https%3A%2F%2Ffaye.vtk.be%3A8443%2Fshibboleth%2F',
        'description' => 'The Shibboleth authentication URL, wherein the target parameter specifies the redirect',
    ),
    array(
        'key'         => 'shibboleth_logout_url',
        'value'       => 'https://idp.kuleuven.be/idp/logout',
        'description' => 'The Shibboleth logout URL, which ends the provider\'s session',
    ),
    array(
        'key'         => 'shibboleth_person_key',
        'value'       => 'Shib_Person_uid',
        'description' => 'The key in the $_SERVER array that accesses the university identification',
    ),
    array(
        'key'         => 'shibboleth_session_key',
        'value'       => 'Shib_Session_Index',
        'description' => 'The key in the $_SERVER array that accesses the shibboleth session',
    ),
    array(
        'key'         => 'shibboleth_code_handler_url',
        'value'       => serialize(
            array(
                'admin'     => 'https://vtk.be/admin/auth/shibboleth/',
                'api'       => 'https://vtk.be/api/oauth/shibboleth/',
                'prof'      => 'https://vtk.be/cudi/prof/auth/shibboleth/',
                'site'      => 'https://vtk.be/auth/shibboleth/',
                'register'  => 'https://vtk.be/secretary/registration/',
                'form'      => 'https://vtk.be/form/manage/auth/shibboleth/',
                'logistics' => 'https://vtk.be/logistics/auth/shibboleth/',
                'wiki'      => 'https://vtk.be/wiki/auth/shibboleth/',
            )
        ),
        'description' => 'The Shibboleth handler URL, without a trailing slash',
    ),
    array(
        'key'         => 'shibboleth_extra_info',
        'value'       => serialize(
            array(
                'first_name' => 'Shib_Person_givenName',
                'last_name'  => 'Shib_Person_surname',
                'email'      => 'Shib_Person_mail',
            )
        ),
        'description' => 'The keys for extra info from Shibboleth',
    ),
    array(
        'key'         => 'student_email_domain',
        'value'       => '@student.kuleuven.be',
        'description' => 'The domain for the student email',
    ),
    array(
        'key'         => 'system_administrator_mail',
        'value'       => 'it@vtk.be',
        'description' => 'The mail address of system administrator',
    ),
    array(
        'key'         => 'fallback_language',
        'value'       => 'nl',
        'description' => 'The abbreviation of the language that will be used of no other translation is present',
    ),
    array(
        'key'         => 'common.cron_log',
        'value'       => '/var/log/litus/cron.log',
        'description' => 'The absolute path of the cron logfile',
    ),
    array(
        'key'         => 'common.sockets_log',
        'value'       => '/var/log/litus/sockets.log',
        'description' => 'The absolute path of the sockets logfile',
    ),
    array(
        'key'         => 'common.geocoding_api_url',
        'value'       => 'http://maps.googleapis.com/maps/api/geocode/',
        'description' => 'The URL to Google\'s geocoding API',
    ),
    array(
        'key'         => 'common.static_maps_api_url',
        'value'       => 'http://maps.googleapis.com/maps/api/staticmap',
        'description' => 'The URL to Google\'s static maps API',
    ),
    array(
        'key'         => 'common.sport_info_on_homepage',
        'value'       => '0',
        'description' => 'Show sport information on homepage',
    ),
    array(
        'key'         => 'common.enable_fathom',
        'value'       => '0',
        'description' => 'Whether or not Fathom support should be enabled',
    ),
    array(
        'key'         => 'common.fathom_url',
        'value'       => 'fathom.studentit.be',
        'description' => 'The URL of the Fathom instance',
    ),
    array(
        'key'         => 'common.fathom_site_id',
        'value'       => 'YWHGL',
        'description' => 'The site ID that was generated by Fathom',
    ),
    array(
        'key'         => 'common.robots',
        'value'       => '#
# Robots File
#

# Taking care of AJAX locations
User-agent: *
Disallow: /*/calendar/month/
Disallow: /*/run/group/getName/
Disallow: /*/career/company/search/

# We do not want them to access our admin
Disallow: /admin/*
',
        'description' => 'The robots.txt content',
    ),
    array(
        'key'         => 'common.privacy_policy',
        'value'       => serialize(
            array(
                'nl' => '## Verantwoordelijke voor de verwerking

VTK vzw hecht zeer veel belang aan de bescherming van de persoonsgegevens van de bezoekers van de website en in het bijzonder van haar studenten. We doen dan ook onze uiterste best om deze gegevens te beschermen en in overeenstemming te zijn met de relevante wetgeving.

Voor vragen omtrent de verwerking van uw persoonsgegevens kan u zich altijd wenden tot VTK vzw:

    VTK vzw t.a.v. VTK IT
    Studentenwijk Arenberg 6/0
    3001 Heverlee
    it@vtk.be

Om uw gegevens te bekijken en desgevallend te corrigeren kan u altijd aanmelden op de VTK-website. Mocht u geen login hebben of problemen ondervinden dan kan u altijd contact opnemen met ons op de hierboven vermelde coördinaten.

Indien u dit wenst, kan u steeds uw persoonlijke gegevens laten verwijderen uit onze database. Wanneer u dit doet, worden alle velden in onze database die u kunnen identificeren, leeg gemaakt. Indien u dit niet doet, houdt VTK deze bij totdat de vereniging ontbonden wordt.

## Verwerkte gegevens

### CV-boek

Teneinde aan laatstejaarsstudenten de mogelijkheid te bieden zich kenbaar te maken aan de bedrijfswereld, houdt VTK een online CV-boek bij. Uw gegevens worden enkel met uw uitdrukkelijke toelating opgenomen in dit bestand.

U kan deze gegevens online inkijken en aanpassen. Schrapping uit het online CV-boek is altijd mogelijk. Hiervoor kan u contact opnemen met [br@vtk.be](br@vtk.be).

### Leden-, studenten- en alumnibestand

De volgende gegevens worden verzameld:

* voornaam;
* naam;
* geboortedatum;
* geslacht;
* telefoonnummer;
* gebruikersnummer van de KU Leuven;
* studenten e-mailadres;
* persoonlijk e-mailadres;
* thuisadres;
* kotadres;
* studierichting.

Deze gegevens worden voor volgende doeleinden gebruikt:

* ledenadministratie;
* klantenbestand cursusdienst;
* toegangscontrole website;
* online diensten (bv. wiki en mailing lists);
* zendingen van bedrijven (zowel per post als via e-mail).

Bij zendingen door bedrijven worden uw gegevens nooit doorgegeven. VTK vzw doet zelf alle zendingen in opdracht van het bedrijf. U kan altijd uw toestemming intrekken om zendingen van bedrijven te ontvangen ([br@vtk.be](br@vtk.be)).

Deze gegevens worden verkregen via volgende wegen:

* registratie op de website;
* aanvraag lidmaatschap bij de secretaris ([secretaris@vtk.be](secretaris@vtk.be)).

Deze gegevens worden aan volgende derden doorgegeven:

* Vlaamse Technische Kring vzw (Studentenwijk Arenberg 6/1, 3001 Heverlee).

U kan deze gegevens op iedere moment kosteloos wijzigen of verwijderen.

## Cookies

VTK gebruikt cookies om het gebruik van de website te analyseren.

Via uw browserinstellingen kunt u de installatie van cookies weigeren. Op de volgende website vindt u voor de meest gebruikte browsers de werkwijze om cookies te weigeren: [www.aboutcookies.org](www.aboutcookies.org).

U kan ook op elk moment de reeds geïnstalleerde cookies verwijderen van uw computer of mobiel apparaat.',
                'en' => '## Responsible for processing

VTK vzw values the protection of personal information of the visitors of our website and her students in particular. We therefore do our utmost to protect this information and to be in accordance with the relevant legislation.

For question about the processing of your personal information, you may address VTK vzw:

    VTK vzw attn VTK IT
    Studentenwijk Arenberg 6/0
    3001 Heverlee
    it@vtk.be

To view your information, and if necessary to correct it, you can always sign in on the VTK-website. If you don\'t have a login or experience problems, you may address us at the coordinates listed above.

If you wish, you can always have your personal information removed from our database. When you do this, all fields in our database that can identify you, will be emptied. If you do not, VTK keeps these up until the organization is dissolved.

## Processed data

### CV-book

In order to give last year students the opportunity to make themselves known to the business world, VTK keeps an online CV book. Your information will only be included with after your explicit consent.

You can view and edit this information online. Removal from the online CV book is possible anytime. You can contact [br@vtk.be](br@vtk.be) to have this done.

### Member-, student- en alumni file

The following data are collected:

* first name;
* name;
* birthday;
* sex;
* phone number;
* username at KU Leuven;
* student mail address;
* personal mail address;
* home address;
* student housing address;
* study.

These data are used for the following purposes:

* Member administration;
* customer base book store;
* access control website;
* online services (bv. wiki and mailing lists);
* consignments of companies (both by mail and e-mail).

Your personal information will never be forwarded in mailings to companies. VTK vzw makes all the mailings commissioned by any company. You can always withdraw your consent to receive company mailings ([br@vtk.be](br@vtk.be)).

This information is obtained from the following sources:

* registration on the website;
* request for membership at the secretary ([secretaris@vtk.be](secretaris@vtk.be)).

These information will be passed to the following third parties:

* Vlaamse Technische Kring vzw (Studentenwijk Arenberg 6/1, 3001 Heverlee).

You can modify or remove this information free of charge at any moment.

## Cookies

VTK uses cookies to analyse the usage of the website.

You can refuse the installation of cookies by changing your browser settings. You can find the procedure to refuse cookies for the most common browsers on the following website: [www.aboutcookies.org](www.aboutcookies.org).

You can remove any installed cookies from your computer or mobile device at any moment.',
            )
        ),
        'description' => 'The privacy policy',
    ),
    array(
        'key'         => 'common.ios_app_id',
        'value'       => '892060967',
        'description' => 'The unique identifier of the iOS app',
    ),
    array(
        'key'         => 'last_upgrade',
        'value'       => '2019011600',
        'description' => 'The last Litus schema update performed on this system',
    ),
    array(
        'key'         => 'common.wiki_button',
        'value'       => '0',
        'description' => 'Boolean that displays the wiki button on the homepage',
    ),
    array(
        'key'         => 'common.poc',
        'value'       => '0',
        'description' => 'Boolean that displays the POC\'ers screen on the homepage',
    ),
    array(
        'key'         => 'common.pocUrl',
        'value'       => '#',
        'description' => 'The url to the poc info page',
    ),
    array(
        'key'         => 'common.pocUrlOverview',
        'value'       => '#',
        'description' => 'The url to the overview page',
    ),
    array(
        'key'         => 'common.save_visits',
        'value'       => '0',
        'description' => 'Flag to log all visits in the database, will create a lot of data!',
    ),
    array(
        'key'         => 'common.show_new_stock_period_warning',
        'value'       => '1',
        'description' => 'Flag to enable/disable the warning to create a new stock period.',
    ),

    array(
        'key'         => 'common.enable_faq',
        'value'       => '0',
        'description' => 'Flag to enable/disable the FAQ\'s on pages.',
    ),
    array(
        'key'         => 'common.slugExpirationInterval',
        'value'       => 'P6M',
        'description' => 'DatePeriod which determines the standard slug expiry date',
    ),
    array(
        'key'         => 'common.kbc_secret_info',
        'value'       => serialize(
            array(
                'shaIn'     => '',
                'shaOut'    => '',
                'urlPrefix' => '',
            )
        ),
        'description' => 'The shaIn, shaOut and urlPrefix for KBC',
    ),
    array(
        'key'       => 'common.studcard_client_id',
        'value'     => 'vtk-cursusdienst',
        'description' => 'Client ID for Student Card API basic authentication',
    ),
    array(
        'key'       => 'common.studcard_client_secret',
        'value'     => 'Secret Value',
        'description' => 'Client Secret for Student Card API basic authentication',
    ),
);
