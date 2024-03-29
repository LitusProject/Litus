<?php

namespace CommonBundle\Component\Authentication;

use CommonBundle\Component\Authentication\AbstractAuthenticationService as AuthenticationService;
use CommonBundle\Component\Authentication\Adapter\Doctrine as DoctrineAdapter;
use RuntimeException;

/**
 * Authentication
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Authentication
{
    /**
     * @var DoctrineAdapter The authentication adapter
     */
    private $adapter = null;

    /**
     * @var AuthenticationService The authentication service
     */
    private $service = null;

    /**
     * @var Result The authentication result
     */
    private $result = null;

    /**
     * Construct a new Authentication object.
     *
     * @param DoctrineAdapter       $adapter The authentication adapter that should be used
     * @param AuthenticationService $service The service that should be used
     */
    public function __construct(DoctrineAdapter $adapter, AuthenticationService $service)
    {
        $this->adapter = $adapter;
        $this->service = $service;
    }

    /**
     * Authenticate the user.
     *
     * @param  string  $identity   The provided identity
     * @param  string  $credential The provided credential
     * @param  boolean $rememberMe Remember this authentication session
     * @param  boolean $shibboleth Whether or not this is sessions initiated by Shibboleth
     * @return void
     */
    public function authenticate($identity = '', $credential = '', $rememberMe = false, $shibboleth = false)
    {
        if (isset($this->result) && $identity == '') {
            return;
        }

        if ($identity != '') {
            $this->adapter
                ->setIdentity($identity)
                ->setCredential($credential);
        }

        $this->result = $this->service->authenticate($this->adapter, $rememberMe, $shibboleth);
    }

    /**
     * Forget the current user.
     *
     * @return \CommonBundle\Entity\User\Session|null
     */
    public function forget()
    {
        $session = $this->service->clearIdentity();
        unset($this->result);

        return $session;
    }

    /**
     * Returns true if the user has been authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        if (isset($this->result)) {
            return $this->result->isValid();
        }

        $this->authenticate();

        if (!isset($this->result)) {
            return false;
        }

        return $this->result->isValid();
    }

    /**
     * Checks whether external sites can access this authentication.
     *
     * @return boolean
     */
    public function isExternallyAuthenticated()
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return $this->service->isExternallyVisible();
    }

    /**
     * Return the person object.
     *
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPersonObject()
    {
        $this->authenticate();

        if (!isset($this->result)) {
            throw new RuntimeException('No user was authenticated');
        }

        return $this->result->getPersonObject();
    }

    /**
     * Return the session object.
     *
     * @return \CommonBundle\Entity\User\Session|null
     */
    public function getSessionObject()
    {
        $this->authenticate();

        if (!isset($this->result)) {
            return null;
        }

        return $this->result->getSessionObject();
    }
}
