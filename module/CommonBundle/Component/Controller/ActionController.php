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

namespace CommonBundle\Component\Controller;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Language,
    Locale,
    Zend\Mvc\MvcEvent,
    Zend\Paginator\Paginator,
    Zend\View\Model\ViewModel;

/**
 * We extend the basic Zend controller to simplify database access, authentication
 * and some other common functionality.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @method \CommonBundle\Component\Controller\Plugin\FlashMessenger flashMessenger()
 * @method \CommonBundle\Component\Controller\Plugin\HasAccess hasAccess()
 * @method \CommonBundle\Component\Controller\Plugin\Paginator paginator()
 * @method \CommonBundle\Component\Controller\Plugin\Url url()
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 */
class ActionController extends \Zend\Mvc\Controller\AbstractActionController implements AuthenticationAware, DoctrineAware
{
    /**
     * @var Language
     */
    protected $_language = null;

    /**
     * Execute the request.
     *
     * @param  MvcEvent                       $e The MVC event
     * @return array
     * @throws Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->_initAuthenticationService();
        $this->_initControllerPlugins();
        $this->_initFallbackLanguage();
        $this->_initViewHelpers();

        if (null !== $this->initAuthentication())
            return new ViewModel();

        $this->initLocalization();

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $authenticatedPerson = null;
        if ($this->getAuthentication()->isAuthenticated())
            $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        $result = parent::onDispatch($e);

        $result->unionShortName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name');
        $result->language = $this->getLanguage();
        $result->languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
        $result->flashMessenger = $this->flashMessenger();
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
     * @throws Request\Exception\NoXmlHttpRequestException The method was not accessed by a XHR request
     */
    protected function initAjax()
    {
        $headers = $this->getRequest()->getHeaders();
        if (
            !isset($headers['X_REQUESTED_WITH'])
            || 'XMLHttpRequest' != $headers['X_REQUESTED_WITH']->getFieldValue()
        ) {
            throw new Request\Exception\NoXmlHttpRequestException(
                'This page is accessible only through an asynchroneous request'
            );
        }
    }

    private function _initAuthenticationService()
    {
        $this->getServiceLocator()->get('authentication_service')
            ->setRequest($this->getRequest())
            ->setResponse($this->getResponse());
    }

    /**
     * Initializes our custom controller plugins.
     *
     * @return void
     */
    private function _initControllerPlugins()
    {
        // Url Plugin
        $this->url()->setLanguage($this->getLanguage());

        // HasAccess Plugin
        $this->hasAccess()->setDriver(
            new HasAccessDriver(
                $this->_getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->getPersonObject()
            )
        );
    }

    /**
     * Initializes the fallback language and make it the default Locale.
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
                        'The specified fallback language does not exist'
                    )
                );
            } else {
                Locale::setDefault($fallbackLanguage->getAbbrev());
            }
        } catch (\Exception $e) {
            // ignore exceptions
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

        $renderer->plugin('url')
            ->setLanguage($this->getLanguage())
            ->setRouter($this->getServiceLocator()->get('router'));

        // HasAccess View Helper
        $renderer->plugin('hasAccess')->setDriver(
            new HasAccessDriver(
                $this->_getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->getPersonObject()
            )
        );

        // GetParam View Helper
        $renderer->plugin('getParam')->setRouteMatch(
            $this->getEvent()->getRouteMatch()
        );

        // StaticMap View Helper
        $renderer->plugin('staticMap')
            ->setEntityManager($this->getEntityManager());
    }

    /**
     * Modifies the reponse headers for a JSON reponse.
     *
     * @param  array $additionalHeaders Any additional headers that should be set
     * @return void
     */
    protected function initJson(array $additionalHeaders = array())
    {
        unset($additionalHeaders['Content-Type']);

        $headers = $this->getResponse()->getHeaders();

        if ($headers->has('Content-Type'))
            $headers->removeHeader('Content-Type');

        $headers->addHeaders(
            array_merge(
                array(
                    'Content-Type' => 'application/json',
                ),
                $additionalHeaders
            )
        );
    }

    /**
     * Initializes the authentication.
     *
     * @return \Zend\Http\Response|null
     */
    protected function initAuthentication()
    {
        $authenticationHandler = $this->getAuthenticationHandler();
        if (null !== $authenticationHandler) {
            if (
                $this->hasAccess()->toResourceAction($this->getParam('controller'), $this->getParam('action'))
            ) {
                if ($this->getAuthentication()->isAuthenticated()) {
                    if (
                        $authenticationHandler['controller'] == $this->getParam('controller')
                            && $authenticationHandler['action'] == $this->getParam('action')
                    ) {
                        return $this->redirectAfterAuthentication();
                    }
                }
            } else {
                if (!$this->getAuthentication()->isAuthenticated()) {
                    if (
                        $authenticationHandler['controller'] != $this->getParam('controller')
                            && $authenticationHandler['action'] != $this->getParam('action')
                    ) {
                        return $this->redirect()->toRoute(
                            $authenticationHandler['auth_route']
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
        $this->getTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        \Zend\Validator\AbstractValidator::setDefaultTranslator($this->getTranslator());

        if ($this->getAuthentication()->isAuthenticated()) {
            $this->getAuthentication()->getPersonObject()->setLanguage($this->getLanguage());
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Returns the ACL object.
     *
     * @return Acl
     */
    private function _getAcl()
    {
        if (null !== $this->getCache()) {
            if (!$this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
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
    protected function getCurrentAcademicYear($organization = false)
    {
        if ($organization)
            return AcademicYear::getOrganizationYear($this->getEntityManager());

        return AcademicYear::getUniversityYear($this->getEntityManager());
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

        if (!isset($language) && isset($this->getSessionStorage()->language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getSessionStorage()->language);
        }

        if (!isset($language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('nl');

            if (null === $language) {
                $language = new Language(
                    'nl', 'Nederlands'
                );

                $this->getEntityManager()->persist($language);
                $this->getEntityManager()->flush();
            }
        }

        $this->getSessionStorage()->language = $language->getAbbrev();

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
     * Gets a parameter from a GET request.
     *
     * @param  string $param   The parameter's key
     * @param  mixed  $default The default value, returned when the parameter is not found
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
     * @return \Zend\Mvc\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }

    /**
     * Redirects after a successful authentication.
     * If this returns null, no redirection will take place.
     *
     * @return void
     */
    protected function redirectAfterAuthentication()
    {
        return $this->redirect()->toRoute(
            $this->getAuthenticationHandler()['redirect_route']
        );
    }
}
