<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Address\City,
    CommonBundle\Entity\General\Address\Street,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\General\Organization;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->_installLanguages();
        $this->_installCities();
        $this->_installOrganizations();

        $this->installConfig(
            array(
                array(
                    'key'         => 'common.profile_path',
                    'value'       => 'data/common/profile',
                    'description' => 'The path for profile photo\'s',
                ),
                array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),
                array(
                    'key'         => 'common.account_deactivated_mail',
                    'value'       => 'Dear,

Your account on VTK has been deactivated.
Click here to activate it again: http://litus/account/activate/code/{{ code }}',
                    'description' => 'The email sent when an account is deactivated',
                ),
                array(
                    'key'         => 'common.account_deactivated_subject',
                    'value'       => 'Account Deactivated',
                    'description' => 'The mail subject when an account is deactivated',
                ),
                array(
                    'key'         => 'common.account_activated_mail',
                    'value'       => 'Dear {{ name }},

An account for you was created on VTK with username {{ username }}.
Click here to activate it: http://litus/account/activate/code/{{ code }}',
                    'description' => 'The email sent when an account is activated',
                ),
                array(
                    'key'         => 'common.account_activated_subject',
                    'value'       => 'Account Created',
                    'description' => 'The mail subject when an account is activated',
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
                    'key'         => 'union_short_name',
                    'value'       => 'VTK',
                    'description' => 'The short name of this union',
                ),
                array(
                    'key'         => 'union_name',
                    'value'       => 'VTK vzw',
                    'description' => 'The full name of this union',
                ),
                array(
                    'key'         => 'union_logo',
                    'value'       => 'data/images/logo/logo.svg',
                    'description' => 'The path to the logo of the union',
                ),
                array(
                    'key'         => 'union_address',
                    'value'       => 'Studentenwijk Arenberg 6/0, 3001 Heverlee',
                    'description' => 'The address of this union',
                ),
                array(
                    'key'         => 'union_city',
                    'value'       => 'Heverlee',
                    'description' => 'The city where this union is located',
                ),
                array(
                    'key'         => 'union_url',
                    'value'       => 'http://vtk.be',
                    'description' => 'The URL of the union',
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
                    'key'         => 'shibboleth_url',
                    'value'       => 'https://arianna.vtk.be:8443/Shibboleth.sso/Login?target=https%3A%2F%2Farianna.vtk.be%3A8443%2Fshibboleth%2F',
                    'description' => 'The Shibboleth authentication URL, wherein the target parameter specifies the redirect',
                ),
                array(
                    'key'         => 'shibboleth_logout_url',
                    'value'       => 'https://idp.kuleuven.be/idp/logout',
                    'description' => 'The Shibboleth logout URL, which ends the provider\'s session',
                ),
                array(
                    'key'         => 'shibboleth_person_key',
                    'value'       => 'Shib-Person-uid',
                    'description' => 'The key in the $_SERVER array that accesses the university identification',
                ),
                array(
                    'key'         => 'shibboleth_session_key',
                    'value'       => 'Shib-Session-ID',
                    'description' => 'The key in the $_SERVER array that accesses the shibboleth session',
                ),
                array(
                    'key'         => 'shibboleth_code_handler_url',
                    'value'       => serialize(
                        array(
                            'admin'     => 'https://dev.vtk.be/admin/auth/shibboleth/',
                            'prof'      => 'https://dev.vtk.be/cudi/prof/auth/shibboleth/',
                            'site'      => 'https://dev.vtk.be/auth/shibboleth/',
                            'register'  => 'https://dev.vtk.be/secretary/registration/',
                            'form'      => 'https://dev.vtk.be/form/manage/auth/shibboleth/',
                            'logistics' => 'https://dev.vtk.be/logistics/auth/shibboleth/',
                            'wiki'      => 'https://dev.vtk.be/wiki/auth/shibboleth/',
                        )
                    ),
                    'description' => 'The Shibboleth handler URL, without a trailing slash',
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
                    'key'         => 'common.piwik_enabled',
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
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'commonbundle' => array(
                    'common_admin_academic' => array(
                        'activate', 'add', 'delete', 'edit', 'manage', 'search', 'typeahead'
                    ),
                    'common_admin_auth' => array(
                        'authenticate', 'login', 'logout', 'shibboleth'
                    ),
                    'common_admin_cache' => array(
                        'manage', 'flush'
                    ),
                    'common_admin_config' => array(
                        'edit', 'manage'
                    ),
                    'common_admin_index' => array(
                        'index'
                    ),
                    'common_admin_location' => array(
                        'add', 'edit', 'delete', 'manage'
                    ),
                    'common_admin_person' => array(
                        'typeahead'
                    ),
                    'common_admin_role' => array(
                        'add', 'edit', 'delete', 'deleteMember', 'manage', 'members', 'prune'
                    ),
                    'common_admin_session' => array(
                        'expire'
                    ),
                    'common_admin_unit' => array(
                        'add', 'delete', 'deleteMember', 'edit', 'manage', 'members', 'prune'
                    ),
                    'common_account' => array(
                        'activate', 'edit', 'index', 'photo', 'saveStudies', 'saveSubjects', 'studies', 'subjects'
                    ),
                    'common_session' => array(
                        'manage', 'expire'
                    ),
                    'common_auth' => array(
                        'login', 'logout', 'shibboleth'
                    ),
                    'common_index' => array(
                        'index'
                    ),

                    'all_install' => array(
                        'index'
                    ),
                    'api_install' => array(
                        'index'
                    ),
                    'banner_install' => array(
                        'index'
                    ),
                    'br_install' => array(
                        'index'
                    ),
                    'calendar_install' => array(
                        'index'
                    ),
                    'common_install' => array(
                        'index'
                    ),
                    'cudi_install' => array(
                        'index'
                    ),
                    'form_install' => array(
                        'index'
                    ),
                    'gallery_install' => array(
                        'index'
                    ),
                    'logistics_install' => array(
                        'index'
                    ),
                    'mail_install' => array(
                        'index'
                    ),
                    'news_install' => array(
                        'index'
                    ),
                    'notification_install' => array(
                        'index'
                    ),
                    'on_install' => array(
                        'index'
                    ),
                    'page_install' => array(
                        'index'
                    ),
                    'publication_install' => array(
                        'index'
                    ),
                    'quiz_install' => array(
                        'index'
                    ),
                    'secretary_install' => array(
                        'index'
                    ),
                    'shift_install' => array(
                        'index'
                    ),
                    'sport_install' => array(
                        'index'
                    ),
                    'syllabus_install' => array(
                        'index'
                    ),
                    'ticket_install' => array(
                        'index'
                    ),
                    'wiki_install' => array(
                        'index'
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'common_admin_auth' => array(
                            'authenticate', 'login', 'logout', 'shibboleth'
                        ),
                        'common_auth' => array(
                            'login', 'logout', 'shibboleth'
                        ),
                        'common_index' => array(
                            'index'
                        ),
                        'common_account' => array(
                            'activate'
                        ),
                    ),
                ),
                'student' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'common_account' => array(
                            'edit', 'index', 'saveStudies', 'saveSubjects', 'studies', 'subjects', 'photo',
                        ),
                    ),
                ),
            )
        );
    }

    private function _installLanguages()
    {
        $languages = array(
            'en' => 'English',
            'nl' => 'Nederlands'
        );

        foreach($languages as $abbrev => $name) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($abbrev);

            if (null === $language) {
                $language = new Language($abbrev, $name);
                $this->getEntityManager()->persist($language);
            }
        }

        $this->getEntityManager()->flush();
    }

    private function _installCities()
    {
        $cities = include('config/streets.php');

        foreach($cities as $cityData) {
            $city = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneByPostal($cityData['postal']);

            if (null === $city) {
                $city = new City($cityData['postal'], $cityData['name']);
                $this->getEntityManager()->persist($city);
            }

            foreach($cityData['streets'] as $streetData) {
                $street = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Address\Street')
                    ->findOneByCityAndName($city, $streetData['name']);
                if (null === $street)
                    $this->getEntityManager()->persist(new Street($city, $streetData['register'], $streetData['name']));
            }
        }

        $this->getEntityManager()->flush();
    }

    private function _installOrganizations()
    {
        $currentOrganizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();
        if (sizeof($currentOrganizations) > 0)
            return;

        $organizations = array(
            'VTK',
        );

        foreach($organizations as $name) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneByName($name);

            if (null === $organization) {
                $organization = new Organization($name);
                $this->getEntityManager()->persist($organization);
            }
        }

        $this->getEntityManager()->flush();
    }
}
