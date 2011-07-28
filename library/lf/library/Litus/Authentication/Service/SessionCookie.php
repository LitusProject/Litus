<?php

namespace Litus\Authentication\Service;

use \Litus\Authentication\Action;
use \Litus\Authentication\Adapter;
use \Litus\Authentication\Result;

use \Zend\Authentication\Storage as Storage;
use \Zend\Authentication\Storage\Session as Session;

class SessionCookie extends \Zend\Authentication\AuthenticationService
{
    /**
     * @var \Zend\Authentication\Storage Persistent identity storage handler
     */
    private $_identity = null;

    /**
     * @var \Zend\Authentication\Storage Persistent identity storage handler
     */
    private $_credential = null;

    /**
     * @var int The expiration time of the cookie
     */
    private $_expirationTime = -1;

    /**
     * @var string The suffix used for cookie name of the identity
     */
    private $_cookieIdentitySuffix = '_identity';

    /**
     * @var string The suffix used for cookie name of the credential
     */
    private $_cookieCredentialSuffix = '_credential';

    /**
     * @var string The namespace for the session and cookies
     */
    private $_namespace = '';

    /**
     * @var \Litus\Authentication\Action The action that should be taken after authentication
     */
    private $_action = null;

    /**
     * Constructing the authentication service.
     *
     * @param string $namespace The namespace for the session and cookies
     */
    public function __construct($namespace = '')
    {
        $this->_namespace = $namespace === '' ? Session::NAMESPACE_DEFAULT : $namespace;
    }

    /**
     * Set the storage expiration time (if applicable). Please note that this will reset all persistent
     * storage handlers!
     *
     * @param int $time The expiration time
     * @return void
     */
    public function setExpirationTime($time)
    {
        $this->_expirationTime = $time;

        $this->setIdentity($this->getIdentity(), -1);
        $this->setCredential($this->getCredential(), -1);
    }

    /**
     * Set the action to take after authentication.
     *
     * @param \Litus\Authentication\Action $action The action that should be taken after authentication
     * @return void
     */
    public function setAction(Action $action)
    {
        $this->_action = $action;
    }

    /**
     * Returns the persistent identity storage handler.
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return \Zend\Authentication\Storage
     */
    public function getIdentityStorage()
    {
        if (null === $this->_identity) {
            $this->setIdentityStorage(new Session($this->_namespace, 'identity'));
        }

        return $this->_identity;
    }

    /**
     * Returns the persistent credential storage handler.
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Zend\Authentication\Storage
     */
    public function getCredentialStorage()
    {
        if (null === $this->_credential) {
            $this->setCredentialStorage(new Session($this->_namespace, 'credential'));
        }

        return $this->_credential;
    }

    /**
     * Sets the persistent identity storage handler.
     *
     * @param \Zend\Authentication\Storage $storage The persistent identity storage handler
     * @return \Zend\Authentication\AuthenticationService
     */
    public function setIdentityStorage(Storage $storage)
    {
        $this->_identity = $storage;
        return $this;
    }

    /**
     * Sets the persistent credential storage handler.
     *
     * @param \Zend\Authentication\Storage $storage The persistent credential storage handler
     * @return \Zend\Authentication\AuthenticationService
     */
    public function setCredentialStorage(Storage $storage)
    {
        $this->_credential = $storage;
        return $this;
    }

    /**
     * Returns the identity from storage or cookie or null if no identity is available.
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        $identity = $this->getIdentityStorage();

        if ($identity->isEmpty()) {
            if (isset($_COOKIE[$this->_namespace . $this->_cookieIdentitySuffix])) {
                return $_COOKIE[$this->_namespace . $this->_cookieIdentitySuffix];
            }
            return null;
        }

        return $identity->read();
    }

    /**
     * Returns true if and only if an identity is available from storage or cookie.
     *
     * @return boolean
     */
    public function hasIdentity()
    {
        if ('' == $this->getIdentityStorage()->read()) {
            return isset($_COOKIE[$this->_namespace . $this->_cookieIdentitySuffix]);
        }
        return true;
    }

    /**
     * Saves the identity in a session an eventually in a cookie.
     *
     * @param string $identity The identity that should be saved
     * @return void
     */
    private function setIdentity($identity)
    {
        $this->getIdentityStorage()->write($identity);
        setCookie($this->_namespace . $this->_cookieIdentitySuffix, $identity, time() + $this->_expirationTime);
    }

    /**
     * Clears the identity from persistent storage and cookie.
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->getIdentityStorage()->clear();
        $this->setIdentity(null, -1);
    }

    /**
     * Returns the credential from storage or cookie or null if no identity is available.
     *
     * @return mixed|null
     */
    public function getCredential()
    {
        $credential = $this->getCredentialStorage();

        if ($credential->isEmpty()) {
            if (isset($_COOKIE[$this->_namespace . $this->_cookieCredentialSuffix])) {
                return $_COOKIE[$this->_namespace . $this->_cookieCredentialSuffix];
            }
            return null;
        }

        return $credential->read();
    }

    /**
     * Returns true if and only if a credential is available from storage or cookie.
     *
     * @return boolean
     */
    public function hasCredential()
    {
        if ('' == $this->getCredentialStorage()->read()) {
            return isset($_COOKIE[$this->_namespace . $this->_cookieCredentialSuffix]);
        }
        return true;
    }

    /**
     * Saves the credential in a session an eventually in a cookie.
     *
     * @param string $credential The credential that should be saved
     * @return void
     */
    private function setCredential($credential)
    {
        $this->getCredentialStorage()->write($credential);
        setCookie($this->_namespace . $this->_cookieCredentialSuffix, $credential, time() + $this->_expirationTime);
    }

    /**
     * Clears the credential from persistent storage and cookie
     *
     * @return void
     */
    public function clearCredential()
    {
        $this->getCredentialStorage()->clear();
        $this->setCredential(null, -1);
    }

    /**
     * Authenticates against the Doctrine adapter
     *
     * @param \Litus\Authentication\Adapter $adapter The Doctrine adapter
     * @return \Litus\Authentication\Result
     */
    public function authenticate(Adapter $adapter)
    {
        if ($adapter->hasIdentity()) {
            $result = $adapter->authenticate();
        } else {
            $result = $adapter->setIdentity($this->getIdentity())
                    ->setCredential($this->getCredential())
                    ->authenticate();
        }

        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $this->setIdentity($result->getIdentity());
            $this->setCredential($result->getCredential());
            if (isset($this->_action))
                $this->_action->succeedAction($result);
        } elseif (!$result->isValid()) {
            if (isset($this->_action))
                $this->_action->failedAction($result);
        }
        
        return $result;
    }
}