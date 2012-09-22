<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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
    CommonBundle\Entity\Users\Person,
    DateTime,
    Locale,
    Zend\Cache\StorageFactory,
    Zend\Mvc\MvcEvent,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\ArrayAdapter;

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
        $startExecutionTime = microtime(true);

        $this->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->_initControllerPlugins();
        $this->_initFallbackLanguage();
        $this->_initViewHelpers();

        $this->initAuthentication();
        $this->initLocalization();

        $authenticatedPerson = null;
        if ($this->getAuthentication()->isAuthenticated())
            $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        $result = parent::onDispatch($e);

        $result->language = $this->getLanguage();
        $result->languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
        $result->flashMessenger = $this->flashMessenger();
        $result->authenticatedPerson = $authenticatedPerson;
        $result->authenticated = $this->getAuthentication()->isAuthenticated();
        $result->environment = getenv('APPLICATION_ENV');
        $result->developmentInformation = array(
            'executionTime' => round(microtime(true) - $startExecutionTime, 3) * 1000,
            'doctrineUnitOfWork' => $this->getEntityManager()->getUnitOfWork()->size()
        );
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
            'staticMapUrl', 'CommonBundle\Component\View\Helper\StaticMapUrl'
        );
        $renderer->plugin('staticMapUrl')
            ->setEntityManager($this->getEntityManager());
    }

    /**
     * Initializes the authentication.
     *
     * @return void
     */
    protected function initAuthentication()
    {
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
                    $this->redirect()->toRoute(
                        $this->getAuthenticationHandler()['redirect_route']
                    );
                }
            }
        } else {
            if (!$this->getAuthentication()->isAuthenticated()) {
                var_dump($this->getParam('controller'), $this->getParam('action'));

                if (
                    $this->getAuthenticationHandler()['controller'] != $this->getParam('controller')
                        && $this->getAuthenticationHandler()['action'] != $this->getParam('action')
                ) {
                    $this->redirect()->toRoute(
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

    /**
     * Initializes the localization
     *
     * @return void
     */
    protected function initLocalization()
    {
        $language = $this->getLanguage();

        $this->getTranslator()->setCache($this->getCache());
        $this->getTranslator()->setLocale($this->getLanguage()->getAbbrev());

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
    protected function getCurrentAcademicYear()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneById(2); // @TODO: remove this
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
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], '-')));
        }

        if (!isset($language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');

            if (null === $language) {
                $language = new Language(
                    'en', 'English'
                );
            }
        }

        $this->_language = $language;

        return $language;
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
}
