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

VTK'
                ),
                'nl' => array(
                    'subject' => 'VTK Account Gedeactiveerd',
                    'content' => 'Beste {{ name }},

Uw account werd gedeactiveerd.
Klik hier om deze opnieuw te activeren: http://litus/account/activate/code/{{ code }}

Met vriendelijke groeten,

VTK'
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

VTK'
                ),
                'nl' => array(
                    'subject' => 'VTK Account Geactiveerd',
                    'content' => 'Beste {{ name }},

Een account werd voor u aangemaakt met gebruikersnaam{{ username }}.
Klik hier om deze te activeren: http://litus/account/activate/code/{{ code }}

Met vriendelijke groeten,

VTK'
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
        'key'         => 'organization_url',
        'value'       => 'http://vtk.be',
        'description' => 'The URL of the organization',
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
        'value'       => serialize(
            array(
                'faye' => 'https://faye.vtk.be:8443/Shibboleth.sso/Login?target=https%3A%2F%2Ffaye.vtk.be%3A8443%2Fshibboleth%2F',
                'liv'  => 'https://liv.vtk.be:8443/Shibboleth.sso/Login?target=https%3A%2F%2Fliv.vtk.be%3A8443%2Fshibboleth%2F',
            )
        ),
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
                'last_name' => 'Shib_Person_surname',
                'email' => 'Shib_Person_mail',
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
        'key'         => 'common.enable_piwik',
        'value'       => '0',
        'description' => 'Whether or not Piwik support should be enabled',
    ),
    array(
        'key'         => 'common.piwik_api_url',
        'value'       => 'http://analytics.vtk.be/',
        'description' => 'The URL to the Piwik installation',
    ),
    array(
        'key'         => 'common.piwik_token_auth',
        'value'       => 'd41d8cd98f00b204e9800998ecf8427e',
        'description' => 'The Piwik authentication token',
    ),
    array(
        'key'         => 'common.piwik_id_site',
        'value'       => '1',
        'description' => 'The Piwik ID of the site that should be queried',
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
        'key'         => 'common.passkit_certificates',
        'value'       => serialize(
            array(
                'membership' => array(
                    'path'     => 'data/certificates/private/membership.p12',
                    'password' => '3Vg2Z401YDh514Qw3t4m76SL',
                )
            )
        ),
        'description' => 'The certificates that will be used to to create PassKit signatures',
    ),
    array(
        'key'         => 'common.passkit_pass_type_identifiers',
        'value'       => serialize(
            array(
                'membership' => 'pass.be.vtk.membership',
            )
        ),
        'description' => 'The PassKit pass type identifiers',
    ),
    array(
        'key'         => 'common.passkit_team_identifier',
        'value'       => '83GZ464K6U',
        'description' => 'The team identifier of the Apple developer account that generated the certificates',
    ),

    array(
        'key'         => 'last_upgrade',
        'value'       => '2014040700',
        'description' => 'The last Litus schema update performed on this system',
    ),
);
