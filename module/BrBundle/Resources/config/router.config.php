<?php

return array(
    'routes' => array(
        'br_install' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/br/install/br[/]',
                'defaults' => array(
                    'controller' => 'br_install',
                    'action'     => 'index',
                ),
            ),
        ),

        'br_admin_collaborator' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/collaborator[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_collaborator',
                    'action'     => 'manage',
                ),
            ),
        ),

        'br_admin_company' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_company_event' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company/event[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company_event',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_company_job' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company/job[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company_job',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_company_user' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company/user[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company_user',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_company_logo' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company/logos[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company_logo',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_company_typeahead' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/company/typeahead[/:string][/]',
                'constraints' => array(
                    'string' => '[%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_company',
                    'action'     => 'typeahead',
                ),
            ),
        ),
        'br_admin_cv_entry' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/cv/entry[/:action[/:id][/page/:page][/:academicyear]][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'           => '[0-9]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'page'         => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_cv_entry',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_contract' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/contract[/:action[/:id[/:signed]][/page/:page][/:language]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'signed' => '(true|false)',
                    'page'   => '[0-9]*',
//                    'language'    => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_contract',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_event' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/event[/:action[/:id][/map/:map][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'map'    => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_event',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_event_company' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/event/:event/company[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'event'  => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_event_company',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_event_location' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/event/:event/location[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'event'  => '[0-9]+',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_event_location',
                    'action'     => 'draw',
                ),
            ),
        ),
        'br_admin_event_subscription' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/event/:event/subscription[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'event'  => '[0-9]+',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_event_subscription',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_admin_invoice' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/invoice[/:action[/:id][/:payed][/date/:date][/page/:page][/:language][/:invoiceyear]][/]',
                'constraints' => array(
                    'action'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'          => '[0-9]*',
                    'payed'       => '(true|false)',
                    'date'        => '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    'page'        => '[0-9]*',
                    'language'    => '(en|nl)',
                    'invoiceyear' => '[0-9]{4}',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_invoice',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_order' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/order[/:action[/:id[/:entry]][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'entry'  => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_order',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_overview' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/overview[/:action[/:id]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_overview',
                    'action'     => 'person',
                ),
            ),
        ),
        'br_admin_product' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/product[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_product',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_request' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/request[/:action[/:id[/:approved]]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9]*',
                    'approved' => '(true|false)',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_request',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_communication' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/communication[/:action[/option/:option][/:id][/page/:page]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9]*',
                    'page'   => '[0-9]*',
                    // 'option'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'option' => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_communication',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_job' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/job[/:action[/:id][/page/:page][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'     => '[0-9_-]*',
                    'page'   => '[0-9]*',
                    'field'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'string' => '[a-zA-Z][%a-zA-Z0-9:.,_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_job',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_admin_studentcompanymatch' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/admin/br/match[/:action[/:academicyear][/:id][/:field/:string]][/]',
                'constraints' => array(
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'id'       => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_admin_studentcompanymatch',
                    'action'     => 'manage',
                ),
            ),
        ),
        'br_career_index' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_index',
                    'action'     => 'index',
                ),
            ),
        ),
        'br_career_company' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/company[/:action[/:company][/id/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'company'  => '[a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                    'id'       => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_company',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_career_company_search' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/company/search[/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_company',
                    'action'     => 'search',
                ),
            ),
        ),
        'br_career_event' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/event[/:action[/:id][/code/:code][/match/:match][/page/:page]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'code'     => '[a-z0-9]*',
                    'id'       => '[0-9_-]*',
                    'match'    => '[0-9_-]*',
                    'language' => '(en|nl)',
                    'page'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_event',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_event_fetch' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/career/event/fetch[/:start][/:end][/]',
                'constraints' => array(
                    'start' => '[0-9]*',
                    'end'   => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_event',
                    'action'     => 'fetch',
                ),
            ),
        ),
        'br_career_vacancy' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/vacancy[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9_-]*',
                    'language' => '(en|nl)',
                    'page'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_vacancy',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_career_file' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '/career/company/file/:name[/]',
                'constraints' => array(
                    'name' => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_company',
                    'action'     => 'file',
                ),
            ),
        ),
        'br_career_match' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/match[/:action[/:profile][/type/:type][/match/:match][/wave/:wave]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'type'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'match'    => '[0-9_-]*',
                    'profile'  => '[0-9_-]*',
                    'wave'     => '[0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_match',
                    'action'     => 'overview',
                    'type'       => 'student',
                ),
            ),
        ),
        'br_corporate_index' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate[/:action][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'language'     => '(en|nl)',
                    'image'        => '[a-zA-Z0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_index',
                    'action'     => 'index',
                ),
            ),
        ),
        'br_corporate_cv' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/cv[/:action[/type/:type][/string/:string][/min/:min][/max/:max][/image/:image][/academicyear/:academicyear][/sortby/:sortby]][/]',
                'constraints' => array(
                    'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'academicyear' => '[0-9]{4}-[0-9]{4}',
                    'language'     => '(en|nl)',
                    'image'        => '[a-zA-Z0-9]*',
                    'type'         => '[a-zA-Z]*',
                    'string'       => '[%a-zA-Z0-9:.,_-]*',
                    'min'          => '[0-9]*',
                    'max'          => '[0-9]*',
                    'sortby'       => '[a-zA-Z]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_cv',
                    'action'     => 'grouped',
                ),
            ),
        ),
        'br_corporate_auth' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/auth[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'session'  => '[0-9]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_auth',
                    'action'     => 'login',
                ),
            ),
        ),
        'br_corporate_jobfair' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/jobfair[/:action[/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_jobfair',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_corporate_vacancy' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/vacancy[/:action[/:id][/page/:page]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'       => '[0-9_-]*',
                    'language' => '(en|nl)',
                    'page'     => '[0-9]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_vacancy',
                    'action'     => 'overview',
                ),
            ),
        ),
        'br_corporate_company' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/company[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'session'  => '[0-9]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_company',
                    'action'     => 'edit',
                ),
            ),
        ),
        'br_corporate_match' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/corporate/match[/:action[/:profile][/type/:type][/match/:match][/wave/:wave]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'type'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'match'    => '[0-9_-]*',
                    'profile'  => '[0-9_-]*',
                    'wave'     => '[0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_corporate_match',
                    'action'     => 'overview',
                    'type'       => 'company',
                ),
            ),
        ),
        'br_cv_index' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/cv[/:action][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                ),
                'defaults'    => array(
                    'controller' => 'br_cv_index',
                    'action'     => 'cv',
                ),
            ),
        ),
        'br_career_internshipfair' => array(
            'type'    => 'Laminas\Router\Http\Segment',
            'options' => array(
                'route'       => '[/:language]/career/internshipfair[/:action[/:company][/id/:id]][/]',
                'constraints' => array(
                    'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'company'  => '[a-zA-Z0-9_-]*',
                    'language' => '(en|nl)',
                    'id'       => '[a-zA-Z0-9_-]*',
                ),
                'defaults'    => array(
                    'controller' => 'br_career_internshipfair',
                    'action'     => 'overview',
                ),
            ),
        ),
    ),
    'controllers' => array(
        'br_admin_company'            => 'BrBundle\Controller\Admin\CompanyController',
        'br_admin_company_event'      => 'BrBundle\Controller\Admin\Company\EventController',
        'br_admin_company_job'        => 'BrBundle\Controller\Admin\Company\JobController',
        'br_admin_company_user'       => 'BrBundle\Controller\Admin\Company\UserController',
        'br_admin_company_logo'       => 'BrBundle\Controller\Admin\Company\LogoController',
        'br_admin_cv_entry'           => 'BrBundle\Controller\Admin\CvController',
        'br_admin_communication'      => 'BrBundle\Controller\Admin\CommunicationController',
        'br_admin_job'                => 'BrBundle\Controller\Admin\JobController',

        'br_admin_collaborator'       => 'BrBundle\Controller\Admin\CollaboratorController',
        'br_admin_contract'           => 'BrBundle\Controller\Admin\ContractController',
        'br_admin_event'              => 'BrBundle\Controller\Admin\EventController',
        'br_admin_event_company'      => 'BrBundle\Controller\Admin\Event\CompanyController',
        'br_admin_event_location'     => 'BrBundle\Controller\Admin\Event\LocationController',
        'br_admin_event_subscription' => 'BrBundle\Controller\Admin\Event\SubscriptionController',
        'br_admin_order'              => 'BrBundle\Controller\Admin\OrderController',
        'br_admin_product'            => 'BrBundle\Controller\Admin\ProductController',
        'br_admin_invoice'            => 'BrBundle\Controller\Admin\InvoiceController',
        'br_admin_overview'           => 'BrBundle\Controller\Admin\OverviewController',
        'br_admin_request'            => 'BrBundle\Controller\Admin\RequestController',
        'br_admin_studentcompanymatch' => 'BrBundle\Controller\Admin\StudentCompanyMatchController',

        'br_corporate_index'          => 'BrBundle\Controller\Corporate\IndexController',
        'br_corporate_cv'             => 'BrBundle\Controller\Corporate\CvController',
        'br_corporate_auth'           => 'BrBundle\Controller\Corporate\AuthController',
        'br_corporate_jobfair'        => 'BrBundle\Controller\Corporate\JobfairController',
        'br_corporate_vacancy'        => 'BrBundle\Controller\Corporate\VacancyController',
        'br_corporate_company'        => 'BrBundle\Controller\Corporate\CompanyController',
        'br_corporate_match'          => 'BrBundle\Controller\Corporate\MatchController',

        'br_career_index'             => 'BrBundle\Controller\Career\IndexController',
        'br_career_vacancy'           => 'BrBundle\Controller\Career\VacancyController',
        'br_career_event'             => 'BrBundle\Controller\Career\EventController',
        'br_career_company'           => 'BrBundle\Controller\Career\CompanyController',
        'br_career_internshipfair'    => 'BrBundle\Controller\Career\InternshipfairController',
        'br_career_match'             => 'BrBundle\Controller\Career\MatchController',

        'br_cv_index'                 => 'BrBundle\Controller\CvController',
    ),
);
