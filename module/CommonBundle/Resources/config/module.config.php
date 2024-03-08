<?php

namespace CommonBundle;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Credential as DoctrineCredentialAdapter;
use CommonBundle\Component\Authentication\Adapter\Doctrine\ServiceManager\CredentialFactory as DoctrineCredentialAdapterFactory;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Authentication\Service\Doctrine as DoctrineService;
use CommonBundle\Component\Authentication\Service\ServiceManager\DoctrineFactory as DoctrineServiceFactory;
use CommonBundle\Component\Authentication\ServiceManager\AuthenticationFactory;
use CommonBundle\Component\Cache\ServiceManager\StorageFactory as CacheStorageFactory;
use CommonBundle\Component\Console\ServiceManager\ApplicationFactory as ConsoleApplicationFactory;
use CommonBundle\Component\Controller\Plugin\ServiceManager\PaginatorFactory;
use CommonBundle\Component\Controller\ServiceManager\AbstractActionControllerInitializer;
use CommonBundle\Component\Doctrine\Common\Cache\ServiceManager\RedisCacheFactory as DoctrineRedisCacheFactory;
use CommonBundle\Component\Doctrine\Migrations\Configuration\ServiceManager\ConfigurationFactory as DoctrineMigrationsConfigurationFactory;
use CommonBundle\Component\Form\Factory as FormFactory;
use CommonBundle\Component\Form\ServiceManager\FactoryFactory as FormFactoryFactory;
use CommonBundle\Component\Google\ServiceManager\ClientFactory as GoogleClientFactory;
use CommonBundle\Component\Google\ServiceManager\ServiceFactory as GoogleServiceFactory;
use CommonBundle\Component\Hydrator\HydratorPluginManager;
use CommonBundle\Component\Hydrator\ServiceManager\HydratorPluginManagerFactory;
use CommonBundle\Component\Module\Config;
use CommonBundle\Component\Module\ServiceManager\AbstractInstallerFactory;
use CommonBundle\Component\Redis\Client as RedisClient;
use CommonBundle\Component\Redis\ServiceManager\ClientFactory as RedisClientFactory;
use CommonBundle\Component\Sentry\Client as SentryClient;
use CommonBundle\Component\Sentry\ServiceManager\ClientFactory as SentryClientFactory;
use CommonBundle\Component\Sentry\ServiceManager\RavenClientFactory;
use CommonBundle\Component\Session\ServiceManager\ContainerFactory as SessionContainerFactory;
use CommonBundle\Component\Session\ServiceManager\SessionManagerFactory;
use CommonBundle\Component\Validator\ServiceManager\AbstractValidatorFactory;
use CommonBundle\Component\View\Helper\ServiceManager\AbstractHelperFactory;
use Doctrine\Common\Cache\RedisCache as DoctrineRedisCache;
use Google\Client as GoogleClient;
use Google\Service\Directory as GoogleDirectoryService;
use Google\Service\Groupssettings as GoogleGroupssettingsService;
use Laminas\Cache\Storage\StorageInterface as CacheStorage;
use Laminas\Form\ElementFactory;
use Laminas\I18n\Translator\Resources as TranslatorResources;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mvc\I18n\Translator as MvcTranslator;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Session\Container as SessionContainer;
use Laminas\Session\ManagerInterface;
use Raven_Client;
use Symfony\Component\Console\Application as ConsoleApplication;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'countries', 'validator', 'units'),
        'has_layouts'       => true,
    ),
    array(
        'controllers' => array(
            'initializers' => array(
                AbstractActionControllerInitializer::class,
            ),
        ),
        'service_manager' => array(
            'factories' => array(
                Authentication::class              => AuthenticationFactory::class,
                CacheStorage::class                => CacheStorageFactory::class,
                ConsoleApplication::class          => ConsoleApplicationFactory::class,
                DoctrineCredentialAdapter::class   => DoctrineCredentialAdapterFactory::class,
                DoctrineRedisCache::class          => DoctrineRedisCacheFactory::class,
                DoctrineService::class             => DoctrineServiceFactory::class,
                FormFactory::class                 => FormFactoryFactory::class,
                GoogleClient::class                => GoogleClientFactory::class,
                GoogleDirectoryService::class      => GoogleServiceFactory::class,
                GoogleGroupssettingsService::class => GoogleServiceFactory::class,
                HydratorPluginManager::class       => HydratorPluginManagerFactory::class,
                Raven_Client::class                => RavenClientFactory::class,
                RedisClient::class                 => RedisClientFactory::class,
                Sendmail::class                    => InvokableFactory::class,
                SentryClient::class                => SentryClientFactory::class,
                SessionContainer::class            => SessionContainerFactory::class,
                ManagerInterface::class            => SessionManagerFactory::class,
            ),
            'abstract_factories' => array(
                AbstractInstallerFactory::class,
            ),
            'aliases' => array(
                'authentication'                    => Authentication::class,
                'authentication_credential_adapter' => DoctrineCredentialAdapter::class,
                'authentication_service'            => DoctrineService::class,
                'cache'                             => CacheStorage::class,
                'console'                           => ConsoleApplication::class,
                'google_client'                     => GoogleClient::class,
                'hydrator_plugin_manager'           => HydratorPluginManager::class,
                'mail_transport'                    => Sendmail::class,
                'raven_client'                      => Raven_Client::class,
                'redis_client'                      => RedisClient::class,
                'sentry_client'                     => SentryClient::class,
                'session_container'                 => SessionContainer::class,
                'translator'                        => MvcTranslator::class,

                'doctrine.cache.redis'              => DoctrineRedisCache::class,
            ),
        ),

        'controller_plugins' => array(
            'factories' => array(
                Component\Controller\Plugin\HasAccess::class      => InvokableFactory::class,
                Component\Controller\Plugin\FlashMessenger::class => InvokableFactory::class,
                Component\Controller\Plugin\Paginator::class      => PaginatorFactory::class,
                Component\Controller\Plugin\Url::class            => InvokableFactory::class,
            ),
            'aliases' => array(
                'hasaccess'      => Component\Controller\Plugin\HasAccess::class,
                'hasAccess'      => Component\Controller\Plugin\HasAccess::class,
                'HasAccess'      => Component\Controller\Plugin\HasAccess::class,
                'flashmessenger' => Component\Controller\Plugin\FlashMessenger::class,
                'flashMessenger' => Component\Controller\Plugin\FlashMessenger::class,
                'FlashMessenger' => Component\Controller\Plugin\FlashMessenger::class,
                'paginator'      => Component\Controller\Plugin\Paginator::class,
                'Paginator'      => Component\Controller\Plugin\Paginator::class,
                'url'            => Component\Controller\Plugin\Url::class,
                'Url'            => Component\Controller\Plugin\Url::class,
            ),
        ),
        'filters' => array(
            'invokables' => array(
                Component\Filter\StripCarriageReturn::class,
            ),
            'aliases' => array(
                'stripcarriagereturn' => Component\Filter\StripCarriageReturn::class,
                'stripCarriageReturn' => Component\Filter\StripCarriageReturn::class,
                'StripCarriageReturn' => Component\Filter\StripCarriageReturn::class,
            ),
        ),
        'translator' => array(
            'translation_file_patterns' => array(
                array(
                    'type'     => 'phparray',
                    'base_dir' => TranslatorResources::getBasePath(),
                    'pattern'  => TranslatorResources::getPatternForValidator(),
                ),
            ),
        ),
        'validators' => array(
            'abstract_factories' => array(
                AbstractValidatorFactory::class,
            ),
            'aliases' => array(
                'datecompare'      => Component\Validator\DateCompare::class,
                'dateCompare'      => Component\Validator\DateCompare::class,
                'DateCompare'      => Component\Validator\DateCompare::class,
                'decimal'          => Component\Validator\Decimal::class,
                'Decimal'          => Component\Validator\Decimal::class,
                'fieldlength'      => Component\Validator\FieldLength::class,
                'fieldLength'      => Component\Validator\FieldLength::class,
                'FieldLength'      => Component\Validator\FieldLength::class,
                'fieldlinelength'  => Component\Validator\FieldLineLength::class,
                'fieldLineLength'  => Component\Validator\FieldLineLength::class,
                'FieldLineLength'  => Component\Validator\FieldLineLength::class,
                'noat'             => Component\Validator\NoAt::class,
                'noAt'             => Component\Validator\NoAt::class,
                'NoAt'             => Component\Validator\NoAt::class,
                'notzero'          => Component\Validator\NotZero::class,
                'notZero'          => Component\Validator\NotZero::class,
                'NotZero'          => Component\Validator\NotZero::class,
                'personbarcode'    => Component\Validator\PersonBarcode::class,
                'personBarcode'    => Component\Validator\PersonBarcode::class,
                'PersonBarcode'    => Component\Validator\PersonBarcode::class,
                'phonenumber'      => Component\Validator\PhoneNumber::class,
                'phoneNumber'      => Component\Validator\PhoneNumber::class,
                'PhoneNumber'      => Component\Validator\PhoneNumber::class,
                'positivenumber'   => Component\Validator\PositiveNumber::class,
                'positiveNumber'   => Component\Validator\PositiveNumber::class,
                'PositiveNumber'   => Component\Validator\PositiveNumber::class,
                'price'            => Component\Validator\Price::class,
                'Price'            => Component\Validator\Price::class,
                'requiredcheckbox' => Component\Validator\RequiredCheckbox::class,
                'requiredCheckbox' => Component\Validator\RequiredCheckbox::class,
                'RequiredCheckbox' => Component\Validator\RequiredCheckbox::class,
                'role'             => Component\Validator\Role::class,
                'Role'             => Component\Validator\Role::class,
                'typeaheadperson'  => Component\Validator\Typeahead\Person::class,
                'typeaheadPerson'  => Component\Validator\Typeahead\Person::class,
                'TypeaheadPerson'  => Component\Validator\Typeahead\Person::class,
                'typeaheadcompany'  => Component\Validator\Typeahead\Company::class,
                'typeaheadCompany'  => Component\Validator\Typeahead\Company::class,
                'TypeaheadCompany'  => Component\Validator\Typeahead\Company::class,
                'username'         => Component\Validator\Username::class,
                'Username'         => Component\Validator\Username::class,
                'year'             => Component\Validator\Year::class,
                'Year'             => Component\Validator\Year::class,
                'Page'             => Component\Validator\Page::class,
                'page'             => Component\Validator\Page::class,
            ),
        ),
        'view_manager' => array(
            'template_map'       => array(
                'layout/layout' => __DIR__ . '/../layouts/layout.twig',
                'error/404'     => __DIR__ . '/../views/error/404.twig',
                'error/index'   => __DIR__ . '/../views/error/index.twig',
            ),

            'doctype'            => 'HTML5',

            'not_found_template' => 'error/404',
            'exception_template' => 'error/index',

            'display_exceptions' => in_array(getenv('APPLICATION_ENV'), array('development', 'staging')),
        ),
        'view_helpers' => array(
            'abstract_factories' => array(
                AbstractHelperFactory::class,
            ),
            'aliases' => array(
                'datelocalized' => Component\View\Helper\DateLocalized::class,
                'dateLocalized' => Component\View\Helper\DateLocalized::class,
                'DateLocalized' => Component\View\Helper\DateLocalized::class,
                'getclass'      => Component\View\Helper\GetClass::class,
                'getClass'      => Component\View\Helper\GetClass::class,
                'GetClass'      => Component\View\Helper\GetClass::class,
                'getparam'      => Component\View\Helper\GetParam::class,
                'getParam'      => Component\View\Helper\GetParam::class,
                'GetParam'      => Component\View\Helper\GetParam::class,
                'hasaccess'     => Component\View\Helper\HasAccess::class,
                'hasAccess'     => Component\View\Helper\HasAccess::class,
                'HasAccess'     => Component\View\Helper\HasAccess::class,
                'hideemail'     => Component\View\Helper\HideEmail::class,
                'hideEmail'     => Component\View\Helper\HideEmail::class,
                'HideEmail'     => Component\View\Helper\HideEmail::class,
                'markdown'      => Component\View\Helper\Markdown::class,
                'Markdown'      => Component\View\Helper\Markdown::class,
                'staticmap'     => Component\View\Helper\StaticMap::class,
                'staticMap'     => Component\View\Helper\StaticMap::class,
                'StaticMap'     => Component\View\Helper\StaticMap::class,
                'url'           => Component\View\Helper\Url::class,
                'Url'           => Component\View\Helper\Url::class,
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
        'doctrine_factories' => array(
            'migrations_configuration' => DoctrineMigrationsConfigurationFactory::class,
        ),

        'litus' => array(
            'forms' => array(
                'bootstrap' => array(
                    'factories' => array(
                        Component\Form\Collection::class                  => ElementFactory::class,
                        Component\Form\Fieldset::class                    => ElementFactory::class,

                        Component\Form\Bootstrap\Element\Button::class    => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Checkbox::class  => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Date::class      => ElementFactory::class,
                        Component\Form\Bootstrap\Element\DateTime::class  => ElementFactory::class,
                        Component\Form\Bootstrap\Element\File::class      => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Hidden::class    => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Password::class  => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Radio::class     => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Reset::class     => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Select::class    => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Submit::class    => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Text::class      => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Textarea::class  => ElementFactory::class,
                        Component\Form\Bootstrap\Element\Typeahead::class => ElementFactory::class,
                    ),
                    'aliases' => array(
                        'collection' => Component\Form\Collection::class,
                        'Collection' => Component\Form\Collection::class,
                        'fieldset'   => Component\Form\Fieldset::class,
                        'Fieldset'   => Component\Form\Fieldset::class,

                        'button'     => Component\Form\Bootstrap\Element\Button::class,
                        'Button'     => Component\Form\Bootstrap\Element\Button::class,
                        'checkbox'   => Component\Form\Bootstrap\Element\Checkbox::class,
                        'Checkbox'   => Component\Form\Bootstrap\Element\Checkbox::class,
                        'date'       => Component\Form\Bootstrap\Element\Date::class,
                        'Date'       => Component\Form\Bootstrap\Element\Date::class,
                        'datetime'   => Component\Form\Bootstrap\Element\DateTime::class,
                        'dateTime'   => Component\Form\Bootstrap\Element\DateTime::class,
                        'DateTime'   => Component\Form\Bootstrap\Element\DateTime::class,
                        'file'       => Component\Form\Bootstrap\Element\File::class,
                        'File'       => Component\Form\Bootstrap\Element\File::class,
                        'hidden'     => Component\Form\Bootstrap\Element\Hidden::class,
                        'Hidden'     => Component\Form\Bootstrap\Element\Hidden::class,
                        'password'   => Component\Form\Bootstrap\Element\Password::class,
                        'Password'   => Component\Form\Bootstrap\Element\Password::class,
                        'radio'      => Component\Form\Bootstrap\Element\Radio::class,
                        'Radio'      => Component\Form\Bootstrap\Element\Radio::class,
                        'reset'      => Component\Form\Bootstrap\Element\Reset::class,
                        'Reset'      => Component\Form\Bootstrap\Element\Reset::class,
                        'select'     => Component\Form\Bootstrap\Element\Select::class,
                        'Select'     => Component\Form\Bootstrap\Element\Select::class,
                        'submit'     => Component\Form\Bootstrap\Element\Submit::class,
                        'Submit'     => Component\Form\Bootstrap\Element\Submit::class,
                        'text'       => Component\Form\Bootstrap\Element\Text::class,
                        'Text'       => Component\Form\Bootstrap\Element\Text::class,
                        'textarea'   => Component\Form\Bootstrap\Element\Textarea::class,
                        'Textarea'   => Component\Form\Bootstrap\Element\Textarea::class,
                        'typeahead'  => Component\Form\Bootstrap\Element\Typeahead::class,
                        'Typeahead'  => Component\Form\Bootstrap\Element\Typeahead::class,
                    ),
                ),
                'admin' => array(
                    'factories' => array(
                        Component\Form\Collection::class                => ElementFactory::class,
                        Component\Form\Fieldset::class                  => ElementFactory::class,

                        Component\Form\Admin\Element\Checkbox::class    => ElementFactory::class,
                        Component\Form\Admin\Element\Csrf::class        => ElementFactory::class,
                        Component\Form\Admin\Element\Date::class        => ElementFactory::class,
                        Component\Form\Admin\Element\DateTime::class    => ElementFactory::class,
                        Component\Form\Admin\Element\File::class        => ElementFactory::class,
                        Component\Form\Admin\Element\Hidden::class      => ElementFactory::class,
                        Component\Form\Admin\Element\Password::class    => ElementFactory::class,
                        Component\Form\Admin\Element\Radio::class       => ElementFactory::class,
                        Component\Form\Admin\Element\Select::class      => ElementFactory::class,
                        Component\Form\Admin\Element\Submit::class      => ElementFactory::class,
                        Component\Form\Admin\Element\Tabs::class        => ElementFactory::class,
                        Component\Form\Admin\Element\Text::class        => ElementFactory::class,
                        Component\Form\Admin\Element\Textarea::class    => ElementFactory::class,
                        Component\Form\Admin\Element\Time::class        => ElementFactory::class,
                        Component\Form\Admin\Element\Typeahead::class   => ElementFactory::class,

                        Component\Form\Admin\Fieldset\Tabbable::class   => ElementFactory::class,
                        Component\Form\Admin\Fieldset\TabContent::class => ElementFactory::class,
                        Component\Form\Admin\Fieldset\TabPane::class    => ElementFactory::class,
                    ),
                    'aliases' => array(
                        'collection' => Component\Form\Collection::class,
                        'Collection' => Component\Form\Collection::class,
                        'fieldset'   => Component\Form\Fieldset::class,
                        'Fieldset'   => Component\Form\Fieldset::class,

                        'checkbox'   => Component\Form\Admin\Element\Checkbox::class,
                        'Checkbox'   => Component\Form\Admin\Element\Checkbox::class,
                        'csrf'       => Component\Form\Admin\Element\Csrf::class,
                        'Csrf'       => Component\Form\Admin\Element\Csrf::class,
                        'date'       => Component\Form\Admin\Element\Date::class,
                        'Date'       => Component\Form\Admin\Element\Date::class,
                        'datetime'   => Component\Form\Admin\Element\DateTime::class,
                        'dateTime'   => Component\Form\Admin\Element\DateTime::class,
                        'DateTime'   => Component\Form\Admin\Element\DateTime::class,
                        'file'       => Component\Form\Admin\Element\File::class,
                        'File'       => Component\Form\Admin\Element\File::class,
                        'hidden'     => Component\Form\Admin\Element\Hidden::class,
                        'Hidden'     => Component\Form\Admin\Element\Hidden::class,
                        'password'   => Component\Form\Admin\Element\Password::class,
                        'Password'   => Component\Form\Admin\Element\Password::class,
                        'radio'      => Component\Form\Admin\Element\Radio::class,
                        'Radio'      => Component\Form\Admin\Element\Radio::class,
                        'select'     => Component\Form\Admin\Element\Select::class,
                        'Select'     => Component\Form\Admin\Element\Select::class,
                        'submit'     => Component\Form\Admin\Element\Submit::class,
                        'Submit'     => Component\Form\Admin\Element\Submit::class,
                        'tabs'       => Component\Form\Admin\Element\Tabs::class,
                        'Tabs'       => Component\Form\Admin\Element\Tabs::class,
                        'text'       => Component\Form\Admin\Element\Text::class,
                        'Text'       => Component\Form\Admin\Element\Text::class,
                        'textarea'   => Component\Form\Admin\Element\Textarea::class,
                        'Textarea'   => Component\Form\Admin\Element\Textarea::class,
                        'time'       => Component\Form\Admin\Element\Time::class,
                        'Time'       => Component\Form\Admin\Element\Time::class,
                        'typeahead'  => Component\Form\Admin\Element\Typeahead::class,
                        'Typeahead'  => Component\Form\Admin\Element\Typeahead::class,

                        'tabbable'   => Component\Form\Admin\Fieldset\Tabbable::class,
                        'Tabbable'   => Component\Form\Admin\Fieldset\Tabbable::class,
                        'tabcontent' => Component\Form\Admin\Fieldset\TabContent::class,
                        'tabContent' => Component\Form\Admin\Fieldset\TabContent::class,
                        'TabContent' => Component\Form\Admin\Fieldset\TabContent::class,
                        'tabpane'    => Component\Form\Admin\Fieldset\TabPane::class,
                        'tabPane'    => Component\Form\Admin\Fieldset\TabPane::class,
                        'TabPane'    => Component\Form\Admin\Fieldset\TabPane::class,
                    ),
                ),
            ),
        ),
    )
);
