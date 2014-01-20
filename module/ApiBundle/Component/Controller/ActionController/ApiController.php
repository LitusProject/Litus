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
    CommonBundle\Component\Acl\Driver\HasAccess,
    CommonBundle\Component\Controller\DoctrineAware,
    CommonBundle\Component\Controller\Exception\HasNoAccessException,
    CommonBundle\Component\Controller\Exception\RuntimeException,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    DateInterval,
    DateTime,
    Zend\Http\Headers,
    Zend\Mvc\MvcEvent,
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
     * @var \CommonBundle\Entity\User\Person The authenticated person object
     */
    private $_authenticatedPersonObject = null;

    /**
     * @var \CommonBundle\Component\Acl\Driver\HasAccess The access driver
     */
    private $_hasAccessDriver = null;

    /**
     * @var \CommonBundle\Entity\General\Language
     */
    private $_language;

    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->_initFallbackLanguage();
        $this->_initLocalization();

        if ('development' != getenv('APPLICATION_ENV')) {
            if (!$this->getRequest()->isPost()) {
                $this->getResponse()->setStatusCode(400);

                return new ViewModel(
                    array(
                        'error' => (object) array(
                            'message' => 'The API should be accessed using POST requests'
                        )
                    )
                );
            }
        }

        if ($this->_validateKey() || $this->_validateOAuth()) {
            if (
                !$this->_hasAccess(
                    $this->getParam('controller'), $this->getParam('action')
                )
            ) {
                $this->getResponse()->setStatusCode(401);

                return new ViewModel(
                    array(
                        'error' => (object) array(
                            'You do not have sufficient permissions to access this resource'
                        )
                    )
                );
            }
        }

        $this->initJson();

        if (false !== getenv('SERVED_BY')) {
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine('X-Served-By', getenv('SERVED_BY'));
        }

        $result = parent::onDispatch($e);
        $result->setTerminal(true);

        $e->setResult($result);
        return $result;
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

            if (null === $fallbackLanguage) {
                $this->getResponse()->setStatusCode(401);

                return new ViewModel(
                    array(
                        'error' => (object) array(
                            'message' => 'The specified fallback language does not exist'
                        )
                    )
                );
            } else {
                \Locale::setDefault($fallbackLanguage->getAbbrev());
            }
        } catch(\Exception $e) {}
    }

    /**
     * Modifies the reponse headers for a JSON reponse.
     *
     * @param array $additionalHeaders Any additional headers that should be set
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
     * Initializes the localization
     *
     * @return void
     */
    private function _initLocalization()
    {
        $language = $this->getLanguage();

        $this->getTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        $this->getMvcTranslator()->setCache($this->getCache())
            ->setLocale($this->getLanguage()->getAbbrev());

        AbstractValidator::setDefaultTranslator($this->getTranslator());
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
     * @param string $param The parameter's key
     * @param mixed $default The default value, returned when the parameter is not found
     * @return string
     */
    public function getParam($param, $default = null)
    {
        return $this->getEvent()->getRouteMatch()->getParam($param, $default);
    }

    private function _hasAccess($resource, $action)
    {
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        if (null === $this->_hasAccessDriver)
            throw new RuntimeException('The access driver was not yet initiliazed');

        return $this->_hasAccessDriver(
            $resource, $action
        );
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

    private function _validateKey()
    {
        if ('development' != getenv('APPLICATION_ENV')) {
            if (null === $this->getRequest()->getPost('key')) {
                $this->getResponse()->setStatusCode(400);

                return new ViewModel(
                    array(
                        'error' => (object) array(
                            'message' => 'No API key was provided with the request'
                        )
                    )
                );
            }

            $key = $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Key')
                ->findOneActiveByCode($this->getRequest()->getPost('key'));

            $validateKey = $key->validate(
                isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
            );

            if (!$validateKey) {
                $this->getResponse()->setStatusCode(401);

                return new ViewModel(
                    array(
                        'error' => (object) array(
                            'message' => 'The given API key was invalid'
                        )
                    )
                );
            }

            $this->_hasAccessDriver = new HasAccess(
                $this->_getAcl(),
                true,
                $key
            );
        }

        return true;
    }

    private function _validateOAuth()
    {
        return true;
    }
}
