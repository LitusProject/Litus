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

namespace CommonBundle\Component\Authentication\Service;

use CommonBundle\Component\Authentication\Action,
    CommonBundle\Component\Authentication\Result\Doctrine as Result,
    CommonBundle\Entity\Users\Session,
    Doctrine\ORM\EntityManager,
    Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Storage\StorageInterface as StorageInterface;

/**
 * An authentication service that uses a Doctrine result.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine extends \Zend\Authentication\AuthenticationService
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var string The name of the entity that holds the sessions
     */
    private $_entityName = '';

    /**
     * @var int The expiration time for the persistent storage
     */
    private $_expire = -1;

    /**
     * @var string The namespace the storage handlers will use
     */
    private $_namespace = '';

    /**
     * @var string The cookie suffix that is used to store the session cookie
     */
    private $_cookieSuffix = '';

    /**
     * @var \CommonBundle\Component\Authentication\Action The action that should be taken after authentication
     */
    private $_action;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $entityName The name of the entity that holds the sessions
     * @param int $expire The expiration time for the persistent storage
     * @param \Zend\Authentication\Storage\StorageInterface $storage The persistent storage handler
     * @param string $namespace The namespace the storage handlers will use
     * @param string $cookieSuffix The cookie suffix that is used to store the session cookie
     * @throws \CommonBundle\Component\Authentication\Service\Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(
        EntityManager $entityManager, $entityName, $expire, StorageInterface $storage, $namespace, $cookieSuffix
    )
    {
        parent::__construct($storage);

        $this->_entityManager = $entityManager;

        $this->_namespace = $namespace;
        $this->_expire = $expire;
        $this->_cookieSuffix = $cookieSuffix;

        if ('\\' == substr($entityName, 0, 1)) {
            throw new Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->_entityName = $entityName;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param \Zend\Authentication\Adapter $adapter The supplied adapter
     * @param boolean $rememberMe Remember this authentication session
     * @param boolean $shibboleth Whether or not this is sessions initiated by Shibboleth
     *
     * @return \Zend\Authentication\Result
     */
    public function authenticate(AdapterInterface $adapter = null, $rememberMe = true, $shibboleth = false)
    {
        $result = null;

        if ('' == $this->getIdentity()) {
            $adapterResult = $adapter->authenticate();

            if ($adapterResult->isValid()) {
                $sessionEntity = $this->_entityName;
                $newSession = new $sessionEntity(
                    $adapterResult->getPersonObject(),
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['REMOTE_ADDR'],
                    $shibboleth,
                    $this->_expire
                );
                $this->_entityManager->persist($newSession);

                $this->getStorage()->write($newSession->getId());
                if ($rememberMe) {
                    $this->_setCookie($newSession->getId(), time() + $this->_expire);
                } else {
                    $this->_setCookie('', -1);
                }

                $result = $adapterResult;

                if (isset($this->_action))
                    $this->_action->succeededAction($result);
            } else {
                $result = $adapterResult;
                if (isset($this->_action))
                    $this->_action->failedAction($adapterResult);
            }
        } else {
            $session = $this->_entityManager->getRepository($this->_entityName)->findOneById(
                $this->getIdentity()
            );

            if (null !== $session) {
                $sessionValidation = $session->validate(
                    $this->_entityManager,
                    $_SERVER['HTTP_USER_AGENT'],
                    $_SERVER['REMOTE_ADDR']
                );

                if (true !== $sessionValidation) {
                    $this->getStorage()->write($sessionValidation);
                    if ($rememberMe) {
                        $this->_setCookie($sessionValidation, time() + $this->_expire);
                    } else {
                        $this->_setCookie('', -1);
                    }
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

        $this->_entityManager->flush();

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

        $session = $this->_entityManager->getRepository($this->_entityName)->findOneById(
            $this->getIdentity()
        );

        if (null !== $session) {
            $session->deactivate();
            $this->_entityManager->flush();
        }

        $this->getStorage()->clear();

        $this->_setCookie('', -1);

        return $session;
    }

    /**
     * Checks whether or not there is a stored session identity.
     *
     * @return bool
     */
    public function hasIdentity()
    {
        if ($this->getStorage()->isEmpty()) {
            if (isset($_COOKIE[$this->_namespace . '_' . $this->_cookieSuffix]))
                $this->getStorage()->write($_COOKIE[$this->_namespace . '_' . $this->_cookieSuffix]);
        }

        return !$this->getStorage()->isEmpty();
    }

    /**
     * @param \CommonBundle\Component\Authentication\Action The action that should be taken after authentication
     *
     * @return \CommonBundle\Component\Authentication\Service\Doctrine
     */
    public function setAction(Action $action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * Set the authentication cookie.
     *
     * @param string $value The cookie's value
     * @param int $expire The cookie's expiration time
     */
    private function _setCookie($value, $expire)
    {
        setcookie(
            $this->_namespace . '_' . $this->_cookieSuffix,
            '',
            -1,
            '/'
        )
        setcookie(
            $this->_namespace . '_' . $this->_cookieSuffix,
            $value,
            $expire,
            '/',
            preg_replace('/^(.*)\.(.*)\.(.*)$/', '$2.$3', $_SERVER['SERVER_NAME'])
        );
    }
}
