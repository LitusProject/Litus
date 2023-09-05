<?php

namespace CommonBundle\Component\Controller;

use CommonBundle\Component\Acl\Acl;
use CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver;
use CommonBundle\Component\Controller\Exception\RuntimeException;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\AuthenticationTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\CacheTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConfigTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\FormFactoryTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\MailTransportTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\RedisClientTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\RouterTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SentryClientTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SessionContainerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\TranslatorTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ViewRendererTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\General\Visit;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Validator\AbstractValidator;
use Laminas\View\Model\ViewModel;
use Locale;

/**
 * We extend the basic Laminas controller to simplify database access,
 * authentication and some other common functionality.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 *
 * @method \CommonBundle\Component\Controller\Plugin\FlashMessenger flashMessenger()
 * @method \CommonBundle\Component\Controller\Plugin\HasAccess hasAccess()
 * @method \CommonBundle\Component\Controller\Plugin\Paginator paginator()
 * @method \CommonBundle\Component\Controller\Plugin\Url url()
 * @method \Laminas\Http\PhpEnvironment\Response getResponse()
 * @method \Laminas\Http\PhpEnvironment\Request getRequest()
 */
class ActionController extends \Laminas\Mvc\Controller\AbstractActionController implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use AuthenticationTrait;
    use CacheTrait;
    use ConfigTrait;
    use DoctrineTrait;
    use FormFactoryTrait;
    use MailTransportTrait;
    use RedisClientTrait;
    use RouterTrait;
    use SentryClientTrait;
    use SessionContainerTrait;
    use TranslatorTrait;
    use ViewRendererTrait;

    /**
     * @var Language
     */
    protected $language = null;

    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getViewRenderer()
            ->plugin('headMeta')
            ->setCharset('utf-8');

        $this->initAuthenticationService();
        $this->initControllerPlugins();
        $this->initFallbackLanguage();
        $this->initViewHelpers();

        if ($this->initAuthentication() !== null) {
            return new ViewModel();
        }

        $this->logVisit();

        $this->initLocalization();

        if (getenv('SERVED_BY') !== false) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $authenticatedPerson = null;
        if ($this->getAuthentication()->isAuthenticated()) {
            $authenticatedPerson = $this->getAuthentication()->getPersonObject();
        }

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
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new Request\Exception\NoXmlHttpRequestException(
                'This page is accessible only through an asynchroneous request'
            );
        }
    }

    /**
     * @return null
     */
    private function logVisit()
    {
        $saveVisit = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.save_visits');

        if ($saveVisit == '1') {
            $server = $this->getRequest()->getServer();
            $route = $this->getEvent()->getRouteMatch();

            $visit = new Visit(
                $server->get('HTTP_USER_AGENT'),
                $server->get('REQUEST_URI'),
                $server->get('REQUEST_METHOD'),
                $route->getParam('controller'),
                $route->getParam('action'),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
            );

            $this->getEntityManager()->persist($visit);
            $this->getEntityManager()->flush();
        }
    }

    private function initAuthenticationService()
    {
        $this->getAuthenticationService()
            ->setRequest($this->getRequest())
            ->setResponse($this->getResponse());
    }

    /**
     * Initializes our custom controller plugins.
     *
     * @return null
     */
    private function initControllerPlugins()
    {
        // Url Plugin
        $this->url()->setLanguage($this->getLanguage());

        // HasAccess Plugin
        $this->hasAccess()->setDriver(
            new HasAccessDriver(
                $this->getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
            )
        );
    }

    /**
     * Initializes the fallback language and make it the default Locale.
     *
     * @return void
     * @throws RuntimeException
     */
    private function initFallbackLanguage()
    {
        try {
            $fallbackLanguage = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('fallback_language')
                );

            if ($fallbackLanguage === null) {
                $this->flashMessenger()->warn(
                    'Warning',
                    'The specified fallback language does not exist'
                );
            } else {
                Locale::setDefault($fallbackLanguage->getAbbrev());
            }
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to initialize fallback language.');
        }
    }

    /**
     * Initializes our custom view helpers.
     *
     * @return void
     */
    private function initViewHelpers()
    {
        $renderer = $this->getViewRenderer();

        $renderer->plugin('url')
            ->setLanguage($this->getLanguage())
            ->setRouter($this->getRouter());

        // HasAccess View Helper
        $renderer->plugin('hasAccess')->setDriver(
            new HasAccessDriver(
                $this->getAcl(),
                $this->getAuthentication()->isAuthenticated(),
                $this->getAuthentication()->isAuthenticated() ? $this->getAuthentication()->getPersonObject() : null
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

        $contentType = $headers->get('Content-Type');
        if ($contentType instanceof HeaderInterface) {
            $headers->removeHeader($contentType);
        }

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
     * @return \Laminas\Http\Response|null
     */
    protected function initAuthentication()
    {
        $authenticationHandler = $this->getAuthenticationHandler();
        if ($authenticationHandler !== null) {
            if ($this->hasAccess()->toResourceAction($this->getParam('controller'), $this->getParam('action'))
            ) {
                if ($this->getAuthentication()->isAuthenticated()) {
                    if ($authenticationHandler['controller'] == $this->getParam('controller')
                        && $authenticationHandler['action'] == $this->getParam('action')
                    ) {
                        return $this->redirectAfterAuthentication();
                    }
                }
            } else {
                if (!$this->getAuthentication()->isAuthenticated()) {
                    if ($authenticationHandler['controller'] != $this->getParam('controller')
                        && $authenticationHandler['action'] != $this->getParam('action')
                    ) {
                        return $this->redirect()->toRoute(
                            $authenticationHandler['auth_route'],
                            array(
                                'redirect' => urlencode($this->getRequest()->getRequestUri()),
                            ));
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
        $translator = $this->getTranslator()->getTranslator();

        $translator->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        AbstractValidator::setDefaultTranslator($this->getTranslator());

        if ($this->getAuthentication()->isAuthenticated()) {
            $this->getAuthentication()->getPersonObject()->setLanguage($this->getLanguage());
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get the current academic year.
     *
     * @param boolean $organization
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }

    /**
     * Returns the ACL object.
     *
     * @return Acl
     */
    private function getAcl()
    {
        if ($this->getCache() !== null) {
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
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if ($this->language !== null) {
            return $this->language;
        }

        if ($this->getParam('language')) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getParam('language'));
        }

        if (!isset($language) && isset($this->getSessionContainer()->language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($this->getSessionContainer()->language);
        }

        if (!isset($language)) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('nl');

            if ($language === null) {
                $language = new Language(
                    'nl',
                    'Nederlands'
                );

                $this->getEntityManager()->persist($language);
                $this->getEntityManager()->flush();
            }
        }

        $this->getSessionContainer()->language = $language->getAbbrev();

        $this->language = $language;

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
     * Log a message to Sentry.
     *
     * @param  string $message
     * @return void
     */
    protected function logMessage($message)
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getSentryClient()->logMessage($message);
        }
    }

    /**
     * Redirects after a successful authentication.
     * If this returns null, no redirection will take place.
     *
     * @return \Laminas\Http\Response
     */
    protected function redirectAfterAuthentication()
    {
        return $this->redirect()->toRoute(
            $this->getAuthenticationHandler()['redirect_route']
        );
    }

    /**
     * @param  string            $name
     * @param  array|object|null $data
     * @return \CommonBundle\Component\Form\Form
     */
    public function getForm($name, $data = null)
    {
        return $this->getFormFactory()->create(array('type' => $name), $data);
    }

    /**
     * @param  string $entityName
     * @param  string $paramKey
     * @param  string $entityKey
     * @return mixed|null
     */
    protected function getEntityById($entityName, $paramKey = 'id', $entityKey = 'id')
    {
        if ($this->getParam($paramKey) === null) {
            return;
        }

        $entity = $this->getEntityManager()
            ->getRepository($entityName)
            ->findOneBy(
                array(
                    $entityKey => $this->getParam($paramKey),
                )
            );

        if ($entity === null) {
            return;
        }

        return $entity;
    }

    public function getRNumberAPI($serialNr, $cardAppId, $entityManager = null)
    {
        if ($entityManager === null) {
            $entityManager = $this->getEntityManager();
        }
        $bearerToken = $this->getBearerToken($entityManager);

        $url = 'https://account.kuleuven.be/api/v1/idverification';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            'Authorization: Bearer ' . $bearerToken,
            'Content-Type: application/json',
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $body = '{"cardAppId": "' . $cardAppId . '","serialNr": "' . $serialNr . '"}';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($curl);
        return json_decode($response)->userName;
    }

    private function getBearerToken($entityManager)
    {
        $clientId = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.studcard_client_id');
        $clientSecret = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.studcard_client_secret');
        $endpoint = 'https://idp.kuleuven.be/auth/realms/kuleuven/protocol/openid-connect/token';

        $base64 = base64_encode($clientId . ':' . $clientSecret);

        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            'Authorization: Basic ' . $base64,
            'Content-Type: application/x-www-form-urlencoded'
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $body = 'grant_type=client_credentials';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response)->access_token;
    }
}
