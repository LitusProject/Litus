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

namespace CommonBundle\Component\Authentication;

use CommonBundle\Component\Authentication\Action\Doctrine,
    Zend\Authentication\Adapter\AdapterInterface,
    CommonBundle\Component\Authentication\AbstractAuthenticationService as AuthenticationService;

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
     * @var \CommonBundle\Component\Authentication\Adapter\Doctrine The authentication adapter
     */
    private $_adapter = null;

    /**
     * @var \CommonBundle\Component\Authentication\AbstractAuthenticationService The authentication service
     */
    private $_service = null;

    /**
     * @var \CommonBundle\Component\Authentication\Result The authentication result
     */
    private $_result = null;

    /**
     * Construct a new Authentication object.
     *
     * @param \Zend\Authentication\Adapter                                         $adapter The authentication adapter that should be used
     * @param \CommonBundle\Component\Authentication\AbstractAuthenticationService $service The service that should be used
     */
    public function __construct(AdapterInterface $adapter, AuthenticationService $service)
    {
        $this->_adapter = $adapter;
        $this->_service = $service;
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
        if (isset($this->_result) && $identity == '')
            return;

        if ('' != $identity) {
            $this->_adapter
                ->setIdentity($identity)
                ->setCredential($credential);
        }

        $this->_result = $this->_service->authenticate($this->_adapter, $rememberMe, $shibboleth);
    }

    /**
     * Forget the current user.
     *
     * @return void
     */
    public function forget()
    {
        $session = $this->_service->clearIdentity();
        unset($this->_result);

        return $session;
    }

    /**
     * Returns true if the provided user has been authenticated.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        if (isset($this->_result))
            return $this->_result->isValid();

        $this->authenticate();

        if (!isset($this->_result))
            return false;

        return $this->_result->isValid();
    }

    /**
     * Checks whether external sites can access this authentication.
     *
     * @return bool
     */
    public function isExternallyAuthenticated()
    {
        if (!$this->isAuthenticated())
            return false;

        return $this->_service->isExternallyVisible();
    }

    /**
     * Return the person object.
     *
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPersonObject()
    {
        $this->authenticate();

        if (!isset($this->_result))
            return null;

        return $this->_result->getPersonObject();
    }

    /**
     * Return the session object.
     *
     * @return \CommonBundle\Entity\User\Session
     */
    public function getSessionObject()
    {
        $this->authenticate();

        if (!isset($this->_result))
            return null;

        return $this->_result->getSessionObject();
    }
}
