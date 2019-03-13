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

use CommonBundle\Component\Authentication\Adapter\Doctrine\Credential as DoctrineCredentialAdapter;
use CommonBundle\Component\Authentication\Adapter\Doctrine\Service\CredentialFactory as DoctrineCredentialAdapterFactory;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Authentication\AuthenticationService\Doctrine as DoctrineAuthenticationService;
use CommonBundle\Component\Authentication\AuthenticationService\Service\DoctrineFactory as DoctrineAuthenticationServiceFactory;
use CommonBundle\Component\Authentication\Service\AuthenticationFactory;
use CommonBundle\Component\Cache\Service\StorageFactory as CacheStorageFactory;
use CommonBundle\Component\Console\Service\ApplicationFactory as ConsoleApplicationFactory;
use CommonBundle\Component\Controller\Plugin\Service\PaginatorFactory;
use CommonBundle\Component\Controller\Service\AbstractActionControllerInitializer;
use CommonBundle\Component\Doctrine\Common\Cache\Service\RedisCacheFactory as DoctrineRedisCacheFactory;
use CommonBundle\Component\Doctrine\Migrations\Configuration\ServiceManager\ConfigurationFactory as DoctrineMigrationsConfigurationFactory;
use CommonBundle\Component\Encore\Configuration as EncoreConfiguration;
use CommonBundle\Component\Encore\Encore;
use CommonBundle\Component\Encore\Service\ConfigurationFactory as EncoreConfigurationFactory;
use CommonBundle\Component\Encore\Service\EncoreFactory;
use CommonBundle\Component\Form\Factory as FormFactory;
use CommonBundle\Component\Form\FormElementManager;
use CommonBundle\Component\Form\Service\FactoryFactory as FormFactoryFactory;
use CommonBundle\Component\Form\Service\FormElementManagerFactory;
use CommonBundle\Component\Hydrator\HydratorPluginManager;
use CommonBundle\Component\Hydrator\Service\HydratorPluginManagerFactory;
use CommonBundle\Component\Module\Config;
use CommonBundle\Component\Module\Service\AbstractInstallerFactory;
use CommonBundle\Component\Redis\Client as RedisClient;
use CommonBundle\Component\Redis\Configuration as RedisConfiguration;
use CommonBundle\Component\Redis\Service\ClientFactory as RedisClientFactory;
use CommonBundle\Component\Redis\Service\ConfigurationFactory as RedisConfigurationFactory;
use CommonBundle\Component\Sentry\Client as SentryClient;
use CommonBundle\Component\Sentry\Service\ClientFactory as SentryClientFactory;
use CommonBundle\Component\Sentry\Service\RavenClientFactory;
use CommonBundle\Component\Session\Service\ContainerFactory as SessionContainerFactory;
use CommonBundle\Component\Session\Service\SessionManagerFactory;
use CommonBundle\Component\Validator\Service\AbstractValidatorFactory;
use CommonBundle\Component\View\Helper\Service\AbstractHelperFactory;
use Doctrine\Common\Cache\RedisCache as DoctrineRedisCache;
use Raven_Client;
use Symfony\Component\Console\Application as ConsoleApplication;
use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\Form\ElementFactory;
use Zend\I18n\Translator\Resources as TranslatorResources;
use Zend\Mail\Transport\Sendmail;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManagerInterface;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'countries', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'service_manager' => array(
            'factories' => array(
                Authentication::class                => AuthenticationFactory::class,
                CacheStorage::class                  => CacheStorageFactory::class,
                ConsoleApplication::class            => ConsoleApplicationFactory::class,
                DoctrineCredentialAdapter::class     => DoctrineCredentialAdapterFactory::class,
                DoctrineRedisCache::class            => DoctrineRedisCacheFactory::class,
                DoctrineAuthenticationService::class => DoctrineAuthenticationServiceFactory::class,
                Encore::class                        => EncoreFactory::class,
                EncoreConfiguration::class           => EncoreConfigurationFactory::class,
                FormElementManager::class            => FormElementManagerFactory::class,
                FormFactory::class                   => FormFactoryFactory::class,
                HydratorPluginManager::class         => HydratorPluginManagerFactory::class,
                Raven_Client::class                  => RavenClientFactory::class,
                RedisClient::class                   => RedisClientFactory::class,
                RedisConfiguration::class            => RedisConfigurationFactory::class,
                Sendmail::class                      => InvokableFactory::class,
                SentryClient::class                  => SentryClientFactory::class,
                SessionContainer::class              => SessionContainerFactory::class,
                SessionManagerInterface::class       => SessionManagerFactory::class,
            ),
            'abstract_factories' => array(
                AbstractInstallerFactory::class,
            ),
            'aliases' => array(
                'authentication'                    => Authentication::class,
                'authentication_credential_adapter' => DoctrineCredentialAdapter::class,
                'authentication_service'            => DoctrineAuthenticationService::class,
                'cache'                             => CacheStorage::class,
                'console'                           => ConsoleApplication::class,
                'encore'                            => Encore::class,
                'encore_config'                     => EncoreConfiguration::class,
                'hydrator_plugin_manager'           => HydratorPluginManager::class,
                'mail_transport'                    => Sendmail::class,
                'raven_client'                      => Raven_Client::class,
                'redis_client'                      => RedisClient::class,
                'redis_config'                      => RedisConfiguration::class,
                'sentry_client'                     => SentryClient::class,
                'session_container'                 => SessionContainer::class,
                'translator'                        => MvcTranslator::class,

                'doctrine.cache.redis' => DoctrineRedisCache::class,

                'FormElementManager' => FormElementManager::class,
                'FormFactory'        => FormFactory::class,
            ),
        ),

        'controllers' => array(
            'initializers' => array(
                AbstractActionControllerInitializer::class,
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
        'form_elements' => array(
            'factories' => array(
                Component\Form\Fieldset::class => ElementFactory::class,

                Component\Form\Element\Button::class     => ElementFactory::class,
                Component\Form\Element\Collection::class => ElementFactory::class,
                Component\Form\Element\Csrf::class       => ElementFactory::class,
                Component\Form\Element\Checkbox::class   => ElementFactory::class,
                Component\Form\Element\Date::class       => ElementFactory::class,
                Component\Form\Element\DateTime::class   => ElementFactory::class,
                Component\Form\Element\File::class       => ElementFactory::class,
                Component\Form\Element\Hidden::class     => ElementFactory::class,
                Component\Form\Element\Password::class   => ElementFactory::class,
                Component\Form\Element\Radio::class      => ElementFactory::class,
                Component\Form\Element\Select::class     => ElementFactory::class,
                Component\Form\Element\Submit::class     => ElementFactory::class,
                Component\Form\Element\Text::class       => ElementFactory::class,
                Component\Form\Element\Textarea::class   => ElementFactory::class,
                Component\Form\Element\Typeahead::class  => ElementFactory::class,
            ),
            'aliases' => array(
                'fieldset' => Component\Form\Fieldset::class,
                'Fieldset' => Component\Form\Fieldset::class,

                'button'     => Component\Form\Element\Button::class,
                'Button'     => Component\Form\Element\Button::class,
                'csrf'       => Component\Form\Element\Csrf::class,
                'Csrf'       => Component\Form\Element\Csrf::class,
                'checkbox'   => Component\Form\Element\Checkbox::class,
                'Checkbox'   => Component\Form\Element\Checkbox::class,
                'collection' => Component\Form\Element\Collection::class,
                'Collection' => Component\Form\Element\Collection::class,
                'date'       => Component\Form\Element\Date::class,
                'Date'       => Component\Form\Element\Date::class,
                'datetime'   => Component\Form\Element\DateTime::class,
                'dateTime'   => Component\Form\Element\DateTime::class,
                'DateTime'   => Component\Form\Element\DateTime::class,
                'file'       => Component\Form\Element\File::class,
                'File'       => Component\Form\Element\File::class,
                'hidden'     => Component\Form\Element\Hidden::class,
                'Hidden'     => Component\Form\Element\Hidden::class,
                'password'   => Component\Form\Element\Password::class,
                'Password'   => Component\Form\Element\Password::class,
                'radio'      => Component\Form\Element\Radio::class,
                'Radio'      => Component\Form\Element\Radio::class,
                'select'     => Component\Form\Element\Select::class,
                'Select'     => Component\Form\Element\Select::class,
                'submit'     => Component\Form\Element\Submit::class,
                'Submit'     => Component\Form\Element\Submit::class,
                'tabbable'   => Component\Form\Element\Tabbable::class,
                'Tabbable'   => Component\Form\Element\Tabbable::class,
                'tabcontent' => Component\Form\Element\TabContent::class,
                'tabContent' => Component\Form\Element\TabContent::class,
                'TabContent' => Component\Form\Element\TabContent::class,
                'tabpane'    => Component\Form\Element\TabPane::class,
                'tabPane'    => Component\Form\Element\TabPane::class,
                'TabPane'    => Component\Form\Element\TabPane::class,
                'text'       => Component\Form\Element\Text::class,
                'Text'       => Component\Form\Element\Text::class,
                'textarea'   => Component\Form\Element\Textarea::class,
                'Textarea'   => Component\Form\Element\Textarea::class,
                'typeahead'  => Component\Form\Element\Typeahead::class,
                'Typeahead'  => Component\Form\Element\Typeahead::class,
            ),
        ),
        'form_view_helpers' => array(
            'admin' => array(
                'factories' => array(
                    Component\Form\View\Helper\Admin\Form::class              => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormButton::class        => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormCollection::class    => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormElement::class       => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormElementErrors::class => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormRow::class           => InvokableFactory::class,
                    Component\Form\View\Helper\Admin\FormSubmit::class        => InvokableFactory::class,
                ),
                'aliases' => array(
                    'form'              => Component\Form\View\Helper\Admin\Form::class,
                    'Form'              => Component\Form\View\Helper\Admin\Form::class,
                    'formbutton'        => Component\Form\View\Helper\Admin\FormButton::class,
                    'formButton'        => Component\Form\View\Helper\Admin\FormButton::class,
                    'FormButton'        => Component\Form\View\Helper\Admin\FormButton::class,
                    'formcollection'    => Component\Form\View\Helper\Admin\FormCollection::class,
                    'formCollection'    => Component\Form\View\Helper\Admin\FormCollection::class,
                    'FormCollection'    => Component\Form\View\Helper\Admin\FormCollection::class,
                    'formelement'       => Component\Form\View\Helper\Admin\FormElement::class,
                    'formElement'       => Component\Form\View\Helper\Admin\FormElement::class,
                    'FormElement'       => Component\Form\View\Helper\Admin\FormElement::class,
                    'formelementerrors' => Component\Form\View\Helper\Admin\FormElementErrors::class,
                    'formElementErrors' => Component\Form\View\Helper\Admin\FormElementErrors::class,
                    'FormElementErrors' => Component\Form\View\Helper\Admin\FormElementErrors::class,
                    'formrow'           => Component\Form\View\Helper\Admin\FormRow::class,
                    'formRow'           => Component\Form\View\Helper\Admin\FormRow::class,
                    'FormRow'           => Component\Form\View\Helper\Admin\FormRow::class,
                    'formsubmit'        => Component\Form\View\Helper\Admin\FormSubmit::class,
                    'formSubmit'        => Component\Form\View\Helper\Admin\FormSubmit::class,
                    'FormSubmit'        => Component\Form\View\Helper\Admin\FormSubmit::class,

                    'form_button'         => Component\Form\View\Helper\Admin\FormButton::class,
                    'form_collection'     => Component\Form\View\Helper\Admin\FormCollection::class,
                    'form_element'        => Component\Form\View\Helper\Admin\FormElement::class,
                    'form_element_errors' => Component\Form\View\Helper\Admin\FormElementErrors::class,
                    'form_row'            => Component\Form\View\Helper\Admin\FormRow::class,
                    'form_submit'         => Component\Form\View\Helper\Admin\FormSubmit::class,
                ),
            ),
            'bootstrap' => array(
                'factories' => array(
                    Component\Form\View\Helper\Bootstrap\Form::class              => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormButton::class        => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormCheckbox::class      => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormCollection::class    => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormElement::class       => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormElementErrors::class => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormRow::class           => InvokableFactory::class,
                    Component\Form\View\Helper\Bootstrap\FormSubmit::class        => InvokableFactory::class,
                ),
                'aliases' => array(
                    'form'              => Component\Form\View\Helper\Bootstrap\Form::class,
                    'Form'              => Component\Form\View\Helper\Bootstrap\Form::class,
                    'formbutton'        => Component\Form\View\Helper\Bootstrap\FormButton::class,
                    'formButton'        => Component\Form\View\Helper\Bootstrap\FormButton::class,
                    'FormButton'        => Component\Form\View\Helper\Bootstrap\FormButton::class,
                    'formcheckbox'      => Component\Form\View\Helper\Bootstrap\FormCheckbox::class,
                    'formCheckbox'      => Component\Form\View\Helper\Bootstrap\FormCheckbox::class,
                    'FormCheckbox'      => Component\Form\View\Helper\Bootstrap\FormCheckbox::class,
                    'formcollection'    => Component\Form\View\Helper\Bootstrap\FormCollection::class,
                    'formCollection'    => Component\Form\View\Helper\Bootstrap\FormCollection::class,
                    'FormCollection'    => Component\Form\View\Helper\Bootstrap\FormCollection::class,
                    'formelement'       => Component\Form\View\Helper\Bootstrap\FormElement::class,
                    'formElement'       => Component\Form\View\Helper\Bootstrap\FormElement::class,
                    'FormElement'       => Component\Form\View\Helper\Bootstrap\FormElement::class,
                    'formelementerrors' => Component\Form\View\Helper\Bootstrap\FormElementErrors::class,
                    'formElementErrors' => Component\Form\View\Helper\Bootstrap\FormElementErrors::class,
                    'FormElementErrors' => Component\Form\View\Helper\Bootstrap\FormElementErrors::class,
                    'formrow'           => Component\Form\View\Helper\Bootstrap\FormRow::class,
                    'formRow'           => Component\Form\View\Helper\Bootstrap\FormRow::class,
                    'FormRow'           => Component\Form\View\Helper\Bootstrap\FormRow::class,
                    'formsubmit'        => Component\Form\View\Helper\Bootstrap\FormSubmit::class,
                    'formSubmit'        => Component\Form\View\Helper\Bootstrap\FormSubmit::class,
                    'FormSubmit'        => Component\Form\View\Helper\Bootstrap\FormSubmit::class,

                    'form_button'         => Component\Form\View\Helper\Bootstrap\FormButton::class,
                    'form_checkbox'       => Component\Form\View\Helper\Bootstrap\FormCheckbox::class,
                    'form_collection'     => Component\Form\View\Helper\Bootstrap\FormCollection::class,
                    'form_element'        => Component\Form\View\Helper\Bootstrap\FormElement::class,
                    'form_element_errors' => Component\Form\View\Helper\Bootstrap\FormElementErrors::class,
                    'form_row'            => Component\Form\View\Helper\Bootstrap\FormRow::class,
                    'form_submit'         => Component\Form\View\Helper\Bootstrap\FormSubmit::class,
                ),
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
                'datecompare'     => Component\Validator\DateCompare::class,
                'dateCompare'     => Component\Validator\DateCompare::class,
                'DateCompare'     => Component\Validator\DateCompare::class,
                'decimal'         => Component\Validator\Decimal::class,
                'Decimal'         => Component\Validator\Decimal::class,
                'fieldlength'     => Component\Validator\FieldLength::class,
                'fieldLength'     => Component\Validator\FieldLength::class,
                'FieldLength'     => Component\Validator\FieldLength::class,
                'fieldlinelength' => Component\Validator\FieldLineLength::class,
                'fieldLineLength' => Component\Validator\FieldLineLength::class,
                'FieldLineLength' => Component\Validator\FieldLineLength::class,
                'noat'            => Component\Validator\NoAt::class,
                'noAt'            => Component\Validator\NoAt::class,
                'NoAt'            => Component\Validator\NoAt::class,
                'notzero'         => Component\Validator\NotZero::class,
                'notZero'         => Component\Validator\NotZero::class,
                'NotZero'         => Component\Validator\NotZero::class,
                'personbarcode'   => Component\Validator\PersonBarcode::class,
                'personBarcode'   => Component\Validator\PersonBarcode::class,
                'PersonBarcode'   => Component\Validator\PersonBarcode::class,
                'phonenumber'     => Component\Validator\PhoneNumber::class,
                'phoneNumber'     => Component\Validator\PhoneNumber::class,
                'PhoneNumber'     => Component\Validator\PhoneNumber::class,
                'positivenumber'  => Component\Validator\PositiveNumber::class,
                'positiveNumber'  => Component\Validator\PositiveNumber::class,
                'PositiveNumber'  => Component\Validator\PositiveNumber::class,
                'price'           => Component\Validator\Price::class,
                'Price'           => Component\Validator\Price::class,
                'role'            => Component\Validator\Role::class,
                'Role'            => Component\Validator\Role::class,
                'typeaheadperson' => Component\Validator\Typeahead\Person::class,
                'typeaheadPerson' => Component\Validator\Typeahead\Person::class,
                'TypeaheadPerson' => Component\Validator\Typeahead\Person::class,
                'username'        => Component\Validator\Username::class,
                'Username'        => Component\Validator\Username::class,
                'year'            => Component\Validator\Year::class,
                'Year'            => Component\Validator\Year::class,
            ),
        ),
        'view_helpers' => array(
            'abstract_factories' => array(
                AbstractHelperFactory::class,
            ),
            'aliases' => array(
                'assets'        => Component\View\Helper\Assets::class,
                'Assets'        => Component\View\Helper\Assets::class,
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

        'doctrine_factories' => array(
            'migrations_configuration' => DoctrineMigrationsConfigurationFactory::class,
        ),
        'encore' => array(
            'output_path' => __DIR__ . '/../../../../public/_encore',
        ),

        'assetic_configuration' => array(
            'buildOnRequest' => getenv('APPLICATION_ENV') == 'development',
            'debug'          => false,
            'webPath'        => __DIR__ . '/../../../../public/_assetic',
            'cacheEnabled'   => getenv('APPLICATION_ENV') != 'development',
            'cachePath'      => __DIR__ . '/../../../../data/cache',
            'basePath'       => '/_assetic',
        ),
    )
);
