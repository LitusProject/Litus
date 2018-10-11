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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'countries', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'controllers' => array(
            'initializers' => array(
                function (\Interop\Container\ContainerInterface $container, $instance) {
                    if (!$instance instanceof Component\ServiceManager\ServiceLocatorAwareInterface) {
                        return;
                    }

                    $instance->setServiceLocator($container);
                },
            ),
        ),
        'service_manager' => array(
            'invokables' => array(
                'mail_transport'     => 'Zend\Mail\Transport\Sendmail',
                'AsseticCacheBuster' => 'AsseticBundle\CacheBuster\LastModifiedStrategy',
            ),
            'factories' => array(
                'authentication' => function ($serviceManager) {
                    return new Component\Authentication\Authentication(
                        $serviceManager->get('authentication_credentialadapter'),
                        $serviceManager->get('authentication_service')
                    );
                },
                'authentication_doctrinecredentialadapter' => function ($serviceManager) {
                    return new Component\Authentication\Adapter\Doctrine\Credential(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Person',
                        'username'
                    );
                },
                'authentication_doctrineservice' => function ($serviceManager) {
                    return new Component\Authentication\Service\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        'CommonBundle\Entity\User\Session',
                        2678400,
                        $serviceManager->get('authentication_sessionstorage'),
                        'Litus_Auth',
                        'Session',
                        $serviceManager->get('authentication_action')
                    );
                },
                'authentication_doctrineaction' => function ($serviceManager) {
                    return new Component\Authentication\Action\Doctrine(
                        $serviceManager->get('doctrine.entitymanager.orm_default'),
                        $serviceManager->get('mail_transport')
                    );
                },
                'authentication_sessionstorage' => function () {
                    return new \Zend\Authentication\Storage\Session(getenv('ORGANIZATION') . '_Litus_Auth');
                },

                'common_sessionstorage' => function () {
                    return new \Zend\Session\Container(getenv('ORGANIZATION') . '_Litus_Common');
                },

                'console' => 'CommonBundle\Component\Console\Service\ApplicationFactory',

                'litus.hydratormanager' => function($serviceManager) {
                    return new Component\Hydrator\HydratorPluginManager(
                        $serviceManager
                    );
                },

                Component\Form\Factory::class => Component\Form\Service\FactoryFactory::class,
            ),
            'abstract_factories' => array(
                Component\Module\Service\AbstractInstallerFactory::class
            ),
            'aliases' => array(
                'authentication_service'           => 'authentication_doctrineservice',
                'authentication_credentialadapter' => 'authentication_doctrinecredentialadapter',
                'authentication_action'            => 'authentication_doctrineaction',

                'translator' => 'MvcTranslator',
            ),
        ),
        'translator' => array(
            'translation_file_patterns' => array(
                array(
                    'type'     => 'phparray',
                    'base_dir' => \Zend\I18n\Translator\Resources::getBasePath(),
                    'pattern'  => \Zend\I18n\Translator\Resources::getPatternForValidator(),
                ),
            ),
        ),
        'view_manager' => array(
            'template_map' => array(
                'layout/layout' => __DIR__ . '/../layouts/layout.twig',
                'error/404'     => __DIR__ . '/../views/error/404.twig',
                'error/index'   => __DIR__ . '/../views/error/index.twig',
            ),

            'doctype' => 'HTML5',

            'not_found_template' => 'error/404',
            'exception_template' => 'error/index',

            'display_exceptions' => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
        ),
        'view_helpers' => array(
            'invokables' => array(
                'dateLocalized' => 'CommonBundle\Component\View\Helper\DateLocalized',
                'getClass'      => 'CommonBundle\Component\View\Helper\GetClass',
                'getParam'      => 'CommonBundle\Component\View\Helper\GetParam',
                'hasAccess'     => 'CommonBundle\Component\View\Helper\HasAccess',
                'hideEmail'     => 'CommonBundle\Component\View\Helper\HideEmail',
                'staticMap'     => 'CommonBundle\Component\View\Helper\StaticMap',
                'url'           => 'CommonBundle\Component\View\Helper\Url',
            ),
        ),
        'controller_plugins' => array(
            'invokables' => array(
                'hasAccess'      => 'CommonBundle\Component\Controller\Plugin\HasAccess',
                'flashMessenger' => 'CommonBundle\Component\Controller\Plugin\FlashMessenger',
                'url'            => 'CommonBundle\Component\Controller\Plugin\Url',
            ),
            'factories' => array(
                Component\Controller\Plugin\Paginator::class => Component\Controller\Plugin\Service\PaginatorFactory::class,
            ),
            'aliases' => array(
                'paginator' => Component\Controller\Plugin\Paginator::class
            ),
        ),
        'assetic_configuration' => array(
            'buildOnRequest' => getenv('APPLICATION_ENV') == 'development',
            'debug'          => false,
            'webPath'        => __DIR__ . '/../../../../public/_assetic',
            'cacheEnabled'   => getenv('APPLICATION_ENV') != 'development',
            'cachePath'      => __DIR__ . '/../../../../data/cache',
            'basePath'       => '/_assetic/',
        ),
        'authentication_sessionstorage' => array(
            'namespace' => getenv('ORGANIZATION') . '_Litus_Auth',
            'member'    => 'storage',
        ),
        'litus' => array(
            'forms' => array(
                'bootstrap' => array(
                    'factories' => array(
                        Component\Form\Collection::class => \Zend\Form\ElementFactory::class,
                        Component\Form\Fieldset::class   => \Zend\Form\ElementFactory::class,

                        Component\Form\Bootstrap\Element\Button::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Checkbox::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Date::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\DateTime::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\File::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Hidden::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Password::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Radio::class     => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Reset::class     => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Select::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Submit::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Text::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\Textarea::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Bootstrap\Element\TypeAhead::class => \Zend\Form\ElementFactory::class,
                    ),
                    'aliases' => array(
                        'collection' => Component\Form\Collection::class,
                        'fieldset'   => Component\Form\Fieldset::class,

                        'button'    => Component\Form\Bootstrap\Element\Button::class,
                        'checkbox'  => Component\Form\Bootstrap\Element\Checkbox::class,
                        'date'      => Component\Form\Bootstrap\Element\Date::class,
                        'datetime'  => Component\Form\Bootstrap\Element\DateTime::class,
                        'file'      => Component\Form\Bootstrap\Element\File::class,
                        'hidden'    => Component\Form\Bootstrap\Element\Hidden::class,
                        'password'  => Component\Form\Bootstrap\Element\Password::class,
                        'radio'     => Component\Form\Bootstrap\Element\Radio::class,
                        'reset'     => Component\Form\Bootstrap\Element\Reset::class,
                        'select'    => Component\Form\Bootstrap\Element\Select::class,
                        'submit'    => Component\Form\Bootstrap\Element\Submit::class,
                        'text'      => Component\Form\Bootstrap\Element\Text::class,
                        'textarea'  => Component\Form\Bootstrap\Element\Textarea::class,
                        'typeahead' => Component\Form\Bootstrap\Element\TypeAhead::class,
                    ),
                ),
                'admin' => array(
                    'factories' => array(
                        Component\Form\Collection::class => \Zend\Form\ElementFactory::class,
                        Component\Form\Fieldset::class   => \Zend\Form\ElementFactory::class,

                        Component\Form\Admin\Element\Checkbox::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Csrf::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Date::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\DateTime::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\File::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Hidden::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Password::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Radio::class     => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Reset::class     => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Select::class    => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Tabs::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Text::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Textarea::class  => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\Time::class      => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Element\TypeAhead::class => \Zend\Form\ElementFactory::class,

                        Component\Form\Admin\Fieldset\Tabbable::class   => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Fieldset\TabContent::class => \Zend\Form\ElementFactory::class,
                        Component\Form\Admin\Fieldset\TabPane::class    => \Zend\Form\ElementFactory::class,
                    ),
                    'aliases' => array(
                        'collection' => Component\Form\Collection::class,
                        'fieldset'   => Component\Form\Fieldset::class,

                        'checkbox'  => Component\Form\Admin\Element\Checkbox::class,
                        'csrf'      => Component\Form\Admin\Element\Csrf::class,
                        'date'      => Component\Form\Admin\Element\Date::class,
                        'datetime'  => Component\Form\Admin\Element\DateTime::class,
                        'file'      => Component\Form\Admin\Element\File::class,
                        'hidden'    => Component\Form\Admin\Element\Hidden::class,
                        'password'  => Component\Form\Admin\Element\Password::class,
                        'radio'     => Component\Form\Admin\Element\Radio::class,
                        'select'    => Component\Form\Admin\Element\Select::class,
                        'tabs'      => Component\Form\Admin\Element\Tabs::class,
                        'text'      => Component\Form\Admin\Element\Text::class,
                        'textarea'  => Component\Form\Admin\Element\Textarea::class,
                        'time'      => Component\Form\Admin\Element\Time::class,
                        'typeahead' => Component\Form\Admin\Element\TypeAhead::class,

                        'tabpane'    => Component\Form\Admin\Fieldset\Tabbable::class,
                        'tabcontent' => Component\Form\Admin\Fieldset\TabContent::class,
                        'tabpane'    => Component\Form\Admin\Fieldset\TabPane::class,
                    ),
                ),
            ),
        ),
        'filters' => array(
            'invokables' => array(
                'stripcarriagereturn' => 'CommonBundle\Component\Filter\StripCarriageReturn',
            ),
        ),
        'validators' => array(
            'abstract_factories' => array(
                Component\Validator\Service\AbstractValidatorFactory::class
            ),
            'aliases' => array(
                'person_barcode'    => Component\Validator\Person\Barcode::class,
                'typeahead_person'  => Component\Validator\Typeahead\Person::class,
                'date_compare'      => Component\Validator\DateCompare::class,
                'decimal'           => Component\Validator\Decimal::class,
                'field_length'      => Component\Validator\FieldLength::class,
                'field_line_length' => Component\Validator\FieldLineLength::class,
                'not_zero'          => Component\Validator\NotZero::class,
                'phone_number'      => Component\Validator\PhoneNumber::class,
                'positive_number'   => Component\Validator\PositiveNumber::class,
                'price'             => Component\Validator\Price::class,
                'role'              => Component\Validator\Role::class,
                'username'          => Component\Validator\Username::class,
                'year'              => Component\Validator\Year::class,
            ),
        ),
    )
);
