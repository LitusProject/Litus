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

namespace CommonBundle\Component\Controller;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Acl\Driver\HasAccess,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\Util\File,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    DateInterval,
    DateTime,
    Locale,
    Zend\Cache\StorageFactory,
    Zend\Mvc\MvcEvent,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\ArrayAdapter,
    Zend\View\Model\ViewModel;

/**
 * We extend the basic Zend controller to simplify database access, authentication
 * and some other common functionality.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ActionController extends \Zend\Mvc\Controller\AbstractActionController implements AuthenticationAware, DoctrineAware
{
    /**
     * @var \CommonBundle\Entity\General\Language
     */
    private $_language;

    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->_initControllerPlugins();
        $this->_initFallbackLanguage();
        $this->_initViewHelpers();

        if (null !== $this->initAuthentication())
            return new ViewModel();

        $this->initLocalization();

        $authenticatedPerson = null;
        if ($this->getAuthentication()->isAuthenticated())
            $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        $result = parent::onDispatch($e);

        $result->unionShortName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('union_short_name');
        $result->language = $this->getLanguage();
        $result->languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
        $result->flashMessenger = $this->flashMessenger();
        $result->persistentFlashMessages = array();
        $result->authenticatedPerson = $authenticatedPerson;
        $result->authenticated = $this->getAuthentication()->isAuthenticated();
        $result->environment = getenv('APPLICATION_ENV');
        $result->setTerminal(true);

        $e->setResult($result);
        return $result;
    }

    /**
     * Does some initialization work for asynchronously requested actions.
     *
     * @return void
     * @throws \CommonBundle\Component\Controller\Request\Exception\NoXmlHttpRequestException The method was not accessed by a XHR request
     */
    protected function initAjax()
    {
        if (
            !$this->getRequest()->getHeaders()->get('X_REQUESTED_WITH')
            || 'XMLHttpRequest' != $this->getRequest()->getHeaders()->get('X_REQUESTED_WITH')->getFieldValue()
        ) {
            throw new Request\Exception\NoXmlHttpRequestException(
                'This page is accessible only through an asynchroneous request'
            );
        }
    }

    /**
     * Initializes our custom controller plugins.
     *
     * @return void
     */
    private function _initControllerPlugins()
    {
        // Url Plugin
        $this->getPluginManager()->setInvokableClass(
            'url', 'CommonBundle\Component\Controller\Plugin\Url'
        );
        $this->url()->setLanguage($this->getLanguage());

        // HasAccess Plugin
        $this->getPluginManager()->setInvokableClass(
            'hasaccess', 'CommonBundle\Component\Controller\Plugin\HasAccess'
        );
        $this->hasAccess()->setDriver(
            new HasAccess(
                $this->_getAcl(), $this->getAuthentication()
            )
        );

        // Paginator Plugin
        $this->getPluginManager()->setInvokableClass(
            'paginator', 'CommonBundle\Component\Controller\Plugin\Paginator'
        );
    }

    /**
     * Initializes the fallback language and stores it in the Registry so that it is
     * accessible troughout the application.
     *
     * @return void
     */
    private function _initFallbackLanguage()
    {
        try {
            $fallbackLanguage = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('fallback_language')
                );

            if (null === $fallbackLanguage) {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::WARNING,
                        'Warning',
                        'The specified fallback language does not exist!'
                    )
                );
            } else {
                Locale::setDefault($fallbackLanguage->getAbbrev());
            }
        } catch(\Exception $e) {
        }
    }

    /**
     * Initializes our custom view helpers.
     *
     * @return void
     */
    private function _initViewHelpers()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');

        // Url Plugin
        $renderer->getHelperPluginManager()->setInvokableClass(
            'url', 'CommonBundle\Component\View\Helper\Url'
        );

        $renderer->plugin('url')->setLanguage($this->getLanguage())
            ->setRouter($this->getServiceLocator()->get('router'));

        // HasAccess View Helper
        $renderer->getHelperPluginManager()->setInvokableClass(
            'hasaccess', 'CommonBundle\Component\View\Helper\HasAccess'
        );
        $renderer->plugin('hasAccess')->setDriver(
            new HasAccess(
                $this->_getAcl(), $this->getAuthentication()
            )
        );

        // GetParam View Helper
        $renderer->getHelperPluginManager()->setInvokableClass(
            'getparam', 'CommonBundle\Component\View\Helper\GetParam'
        );
        $renderer->plugin('getParam')->setRouteMatch(
            $this->getEvent()->getRouteMatch()
        );

        // Date View Helper
        $renderer->getHelperPluginManager()->setInvokableClass(
            'dateLocalized', 'CommonBundle\Component\View\Helper\DateLocalized'
        );

        // StaticMap View Helper
        $renderer->getHelperPluginManager()->setInvokableClass(
            'staticMap', 'CommonBundle\Component\View\Helper\StaticMap'
        );
        $renderer->plugin('staticMap')
            ->setEntityManager($this->getEntityManager());

        // Hide Email Helper
        $renderer->getHelperPluginManager()->setInvokableClass(
            'hideEmail', 'CommonBundle\Component\View\Helper\HideEmail'
        );
    }

    /**
     * Initializes the authentication.
     *
     * @return void
     */
    protected function initAuthentication()
    {
        if (null !== $this->getAuthenticationHandler()) {
            if (
                $this->hasAccess()->resourceAction(
                    $this->getParam('controller'), $this->getParam('action')
                )
            ) {
                if ($this->getAuthentication()->isAuthenticated()) {
                    if (
                        $this->getAuthenticationHandler()['controller'] == $this->getParam('controller')
                            && $this->getAuthenticationHandler()['action'] == $this->getParam('action')
                    ) {
                        return $this->redirect()->toRoute(
                            $this->getAuthenticationHandler()['redirect_route']
                        );
                    }
                }
            } else {
                if (!$this->getAuthentication()->isAuthenticated()) {
                    if (
                        $this->getAuthenticationHandler()['controller'] != $this->getParam('controller')
                            && $this->getAuthenticationHandler()['action'] != $this->getParam('action')
                    ) {
                        return $this->redirect()->toRoute(
                            $this->getAuthenticationHandler()['auth_route']
                        );
                    }
                } else {
                    throw new Exception\HasNoAccessException(
                        'You do not have sufficient permissions to access this resource'
                    );
                }
            }
        }
    }

    /**
     * Initializes the localization
     *
     * @return void
     */
    protected function initLocalization()
    {
        $language = $this->getLanguage();

        $this->getTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        $this->getMvcTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        \Zend\Validator\AbstractValidator::setDefaultTranslator($this->getTranslator());

        if ($this->getAuthentication()->isAuthenticated()) {
            $this->getAuthentication()->getPersonObject()->setLanguage($language);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Returns the ACL object.
     *
     * @return \CommonBundle\Component\Acl\Acl
     */
    private function _getAcl()
    {
        if (null !== $this->getCache()) {
            if(!$this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
                $acl = new Acl(
                    $this->getEntityManager()
                );

                $this->getCache()->setItem('CommonBundle_Component_Acl_Acl', $acl);
            }

            return $this->getCache()->getItem('CommonBundle_Component_Acl_Acl');
        }

        return new Acl(
            $this->getEntityManager()
        );
    }

    /**
     * We want an easy method to retrieve the Authentication from
     * the DI container.
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication()
    {
        return $this->getServiceLocator()->get('authentication');
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        throw new \RuntimeException(
            'Do not extend \CommonBundle\Component\Controller\ActionController directly'
        );

        return null;
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\Adapter\Apc
     */
    public function getCache()
    {
        if ($this->getServiceLocator()->has('cache'))
            return $this->getServiceLocator()->get('cache');

        return null;
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if (!$organization) {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();
            $startAcademicYear->setTime(0, 0);

            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByUniversityStart($startAcademicYear);

            if (null === $academicYear) {
                $organizationStart = str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                );
                $organizationStart = new DateTime($organizationStart);
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }

            return $academicYear;
        } else {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();
            $startAcademicYear->setTime(0, 0);

            $now = new DateTime();
            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.prof_start_academic_year')
                )
            );
            $start->add(new DateInterval('P1Y'));

            if ($now > $start) {
                $startAcademicYear->add(new DateInterval('P1Y2M'));
                $startAcademicYear = AcademicYear::getStartOfAcademicYear($startAcademicYear);
            }

            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByUniversityStart($startAcademicYear);

            if (null === $academicYear) {
                $organizationStart = str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                );
                $organizationStart = new DateTime($organizationStart);
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }

            return $academicYear;
        }
    }

    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if (null !== $this->_language)
            return $this->_language;

        if ($this->getParam('language')) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getParam('language'));
        }

        if (!isset($language) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (strpos($locale, '_') > 0)
                $locale = substr($locale, 0, strpos($locale, '_'));

            if ($locale) {
                $language = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev($locale);
            }
        }

        if (!isset($language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');

            if (null === $language) {
                $language = new Language(
                    'en', 'English'
                );

                $this->getEntityManager()->persist($language);
                $this->getEntityManager()->flush();
            }
        }

        $this->_language = $language;

        return $language;
    }

    /**
     * Add a persistent flash message
     * @param mixed $result The result of onDispatch
     * @param \CommonBundle\Component\FlashMessenger\FlashMessage $flashMessage The flash message
     */
    protected function addPersistentFlashMessage($result, FlashMessage $flashMessage)
    {
        $result->persistentFlashMessages = array_merge(
            $result->persistentFlashMessages,
            array($flashMessage)
        );
    }

    /**
     * We want an easy method to retrieve the LDAP connection from
     * the DI container.
     *
     * @return \Zend\Ldap\Ldap
     */
    public function getLdap()
    {
        return $this->getServiceLocator()->get('ldap');
    }

    /**
     * We want an easy method to retrieve the Mail Transport from
     * the DI container.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * Gets a parameter from a GET request.
     *
     * @param string $param The parameter's key
     * @param mixed $default The default value, returned when the parameter is not found
     * @return string
     */
    public function getParam($param, $default = null)
    {
        return $this->getEvent()->getRouteMatch()->getParam($param, $default);
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    public function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }

    /**
     * We want an easy method to retrieve the Translator from
     * the DI container.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }

    /**
     * We want an easy method to retrieve the Translator from
     * the DI container.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getMvcTranslator()
    {
        return $this->getServiceLocator()->get('MvcTranslator');
    }
}
