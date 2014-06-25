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

namespace ApiBundle\Component\Controller\ActionController;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver,
    CommonBundle\Component\Controller\DoctrineAware,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    Zend\Mvc\MvcEvent,
    Zend\Uri\UriFactory,
    Zend\Validator\AbstractValidator,
    Zend\View\Model\ViewModel;

/**
 * We extend the CommonBundle controller.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ApiController extends \Zend\Mvc\Controller\AbstractActionController implements DoctrineAware
{
    /**
     * @var \CommonBundle\Component\Acl\Driver\HasAccess The access driver
     */
    private $_hasAccessDriver = null;

    /**
     * @var \CommonBundle\Entity\General\Language The current language
     */
    private $_language = null;

    /**
     * Execute the request.
     *
     * @param  \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->_initControllerPlugins();
        $this->_initFallbackLanguage();
        $this->_initLocalization();
        $this->_initUriScheme();

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $result = parent::onDispatch($e);
        $result->setTerminal(true);

        if ($this->_validateKey() || $this->_validateOAuth()) {
            if ('development' != getenv('APPLICATION_ENV'))
                $this->hasAccess()->setDriver($this->_hasAccessDriver);

            if (
                !$this->hasAccess()->toResourceAction(
                    $this->getParam('controller'), $this->getParam('action')
                )
            ) {
                $error = $this->error(401, 'You do not have sufficient permissions to access this resource');
                $error->setOptions($result->getOptions());
                $e->setResult($error);

                return $error;
            }
        } else {
            $error = $this->error(401, 'No key or OAuth token was provided');
            $error->setOptions($result->getOptions());
            $e->setResult($error);

            return $error;
        }

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $result->flashMessenger = $this->flashMessenger();

        $e->setResult($result);

        return $result;
    }

    /**
     * Returns an error message.
     *
     * @param  integer                    $code    The HTTP status code
     * @param  string                     $message The error message
     * @return \Zend\View\Model\ViewModel
     */
    public function error($code, $message)
    {
        if (!$this->_isAuthorizeAction())
            $this->initJson();

        $this->getResponse()->setStatusCode($code);

        $error = array(
            'message' => $message
        );

        return new ViewModel(
            array(
                'error' => (object) $error
            )
        );
    }

    /**
     * Initializes the fallback language and sets it as the default so that it is
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

            if (null !== $fallbackLanguage)
                \Locale::setDefault($fallbackLanguage->getAbbrev());
        } catch (\Exception $e) {}
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
            $headers->removeHeader($headers->get('Content-Type'));

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
            new HasAccessDriver(
                $this->_getAcl(), false
            )
        );
    }

    /**
     * Initializes the localization.
     *
     * @return void
     */
    private function _initLocalization()
    {
        $this->getTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        AbstractValidator::setDefaultTranslator($this->getTranslator());
    }

    /**
     * Initializes custom URL schemes.
     *
     * @return void
     */
    private function _initUriScheme()
    {
        UriFactory::registerScheme('litus', 'ApiBundle\Component\Uri\Litus');
    }

    /**
     * Checks if the current action is the OAuth authorize action.
     *
     * @return boolean
     */
    private function _isAuthorizeAction()
    {
        return ('authorize' == $this->getParam('action') || 'shibboleth' == $this->getParam('action')) && 'api_oauth' == $this->getParam('controller');
    }

    /**
     * Checks if the current action is an OAuth action.
     *
     * @return boolean
     */
    private function _isOAuthAction()
    {
        return 'api_oauth' == $this->getParam('controller');
    }

    /**
     * Returns the ACL object.
     *
     * @return \CommonBundle\Component\Acl\Acl
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
     * Helper method that retrieves the API key.
     *
     * @param  string                $field The name of the field that contains the key
     * @return \ApiBundle\Entity\Key
     */
    protected function getKey($field = 'key')
    {
        if ($this->_isOAuthAction())
            $field = 'client_id';

        $code = $this->getRequest()->getQuery($field);
        if (null === $code && $this->getRequest()->isPost())
            $code = $this->getRequest()->getPost($field);

        $key = $this->getEntityManager()
            ->getRepository('ApiBundle\Entity\Key')
            ->findOneActiveByCode($code);

        return $key;
    }

    /**
     * Helper method that retrieves the OAuth 2 access token.
     *
     * @param  string                           $field The name of the field that contains the access token
     * @return \ApiBundle\Document\Token\Access
     */
    protected function getAccessToken($field = 'access_token')
    {
        $code = $this->getRequest()->getQuery($field);
        if (null === $code && $this->getRequest()->isPost())
            $code = $this->getRequest()->getPost($field);

        $accessToken = $this->getDocumentManager()
            ->getRepository('ApiBundle\Document\Token\Access')
            ->findOneActiveByCode($code);

        return $accessToken;
    }

    /**
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if ('' != $this->getRequest()->getPost('language', '')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getRequest()->getPost('language'));
        }

        if (null !== $this->_language)
            return $this->_language;

        if ($this->getParam('language')) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getParam('language'));
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

        $this->_language = $language;

        return $language;
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
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }

    /**
     * Authenticates an application if an API key is provided.
     *
     * @return boolean
     */
    private function _validateKey()
    {
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        if ($this->_isAuthorizeAction()) {
            $this->_hasAccessDriver = new HasAccessDriver(
                $this->_getAcl(),
                false,
                null
            );

            return true;
        }

        $key = $this->getKey();
        if (null === $key)
            return false;

        $validateKey = $key->validate(
            $this->getRequest()->getServer('HTTP_X_FORWARDED_FOR', $this->getRequest()->getServer('REMOTE_ADDR'))
        );

        if (!$validateKey)
            return false;

        $this->_hasAccessDriver = new HasAccessDriver(
            $this->_getAcl(),
            true,
            $key
        );

        return true;
    }

    /**
     * Authenticates a user if an OAuth access token is provided.
     *
     * @return boolean
     */
    private function _validateOAuth()
    {
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        if ($this->_isAuthorizeAction()) {
            $this->_hasAccessDriver = new HasAccessDriver(
                $this->_getAcl(),
                false,
                null
            );

            return true;
        }

        $accessToken = $this->getAccessToken();
        if (null === $accessToken)
            return false;

        $this->_hasAccessDriver = new HasAccessDriver(
            $this->_getAcl(),
            true,
            $accessToken->getPerson($this->getEntityManager())
        );

        return true;
    }
}
