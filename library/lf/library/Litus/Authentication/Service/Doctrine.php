<?php

namespace Litus\Authentication\Service;

use \Litus\Authentication\Action;
use \Litus\Authentication\Result\Doctrine as Result;
use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Entity\Users\Session;

use \Zend\Authentication\Adapter;
use \Zend\Authentication\Storage as Storage;
use \Zend\Authentication\Storage\Session as SessionStorage;
use \Zend\Registry;

class Doctrine extends \Zend\Authentication\AuthenticationService
{
    /**
     * The default namespace that should be used
     */
    const NAMESPACE_DEFAULT = 'Litus_Auth';

    /**
     * The default suffix that is used to name the session cookie
     */
    const COOKIE_SUFFIX_DEFAULT = '_Session';

    /**
     * @var string The namespace the storage handlers will use
     */
    private $_namespace = '';

    /**
     * @var int The expiration time for the persistent storage
     */
    private $_expire = -1;

    /**
     * @var string The cookie suffix that is used to store the session cookie
     */
    private $_cookieSuffix = '';

    /**
     * @var string The name of the entity that holds the sessions
     */
    private $_entityName = '';

    /**
     * @param string $entityName The name of the entity that holds the sessions
     * @param int $expire The expiration time for the persistent storage
     * @param \Zend\Authentication\Storage $storage The persistent storage handler
     * @param string $namespace The namespace the storage handlers will use
     * @param string $cookieSuffix The cookie suffix that is used to store the session cookie
     */
    public function __construct(
        $entityName, $expire = -1, Storage $storage = null, $namespace = self::NAMESPACE_DEFAULT,
        $cookieSuffix = self::COOKIE_SUFFIX_DEFAULT
    )
    {
        parent::__construct($storage);

        $this->_namespace = $namespace;
        $this->_expire = $expire;
        $this->_cookieSuffix = $cookieSuffix;

        if ('\\' == substr($entityName, 0, 1)) {
            throw new \Litus\Authentication\Service\Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->_entityName = $entityName;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param \Zend\Authentication\Adapter $adapter The supplied adapter
     * @return \Zend\Authentication\Result
     */
    public function authenticate(Adapter $adapter)
    {
        $entityManager = Registry::get(DoctrineResource::REGISTRY_KEY);

        $result = null;
        if ('' == $this->getIdentity()) {
            $adapterResult = $adapter->authenticate();

            if ($adapterResult->isValid()) {
                $sessionEntity = $this->_entityName;
                $newSession = new $sessionEntity(
                    $this->_expire,
                    $adapterResult->getPersonObject(),
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['REMOTE_ADDR']
                );
                $entityManager->persist($newSession);

                $this->getStorage()->write($newSession->getId());
                setcookie(
                    $this->_namespace . $this->_cookieSuffix, $newSession->getId(), time() + $this->_expire
                );

                $result = $adapterResult;
            }
        } else {
            $session = $entityManager->getRepository($this->_entityName)->findOneById(
                $this->getIdentity()
            );

            if (null !== $session) {
                $sessionValidation = $session->validateSession(
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['REMOTE_ADDR']
                );

                if (true !== $sessionValidation) {
                    $this->getStorage()->write($sessionValidation);
                    setcookie(
                        $this->_namespace . $this->_cookieSuffix, $sessionValidation, time() + $this->_expire
                    );
                }

				$result = new Result(
                    Result::SUCCESS,
                    $session->getPerson()->getUsername(),
                    array(
                         'Authentication successful'
                    ),
                    $session->getPerson()
                );
            } else {
                $this->clearIdentity();
            }
        }

        $entityManager->flush();

        return $result;
    }

    /**
     * Returns the session identity.
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->hasIdentity() ? $this->getStorage()->read() : '';
    }

    /**
     * Clears the persistent storage and deactivates the associated session.
     *
     * @return void
     */
    public function clearIdentity()
    {
        if (!$this->hasIdentity())
            return;

        $entityManager = Registry::get('EntityManager');
        $session = $entityManager->getRepository($this->_entityName)->findOneById(
            $this->getIdentity()
        );

        if (null !== $session) {
            $session->deactivate();
        }

        $this->getStorage()->clear();
        setcookie(
            $this->_namespace . $this->_cookieSuffix, '', -1
        );
    }

    /**
     * Checks whether or not there is a stored session identity.
     *
     * @return bool
     */
    public function hasIdentity()
    {
        if ($this->getStorage()->isEmpty()) {
            if (isset($_COOKIE[$this->_namespace . $this->_cookieSuffix]))
                $this->getStorage()->write($_COOKIE[$this->_namespace . $this->_cookieSuffix]);
        }
        return !$this->getStorage()->isEmpty();
    }
}
