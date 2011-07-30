<?php

namespace Litus\Authentication;

use \Litus\Authentication\Action;
use \Litus\Authentication\Result\Doctrine;

use \Zend\Authentication\Adapter;
use \Zend\Authentication\AuthenticationService;

class Authentication
{
	/**
     * @var \Litus\Authentication\Adapter\Doctrine The authentication adapter
     */
	private $_adapter = null;
	
	/**
     * @var \Zend\Authentication\AuthenticationService The authentication service
     */
	private $_service = null;
	
	/**
     * @var \Litus\Authentication\Result The authentication result
     */
	private $_result = null;
	
    /**
     * Construct a new Authentication object.
     *
     * @param \Zend\Authentication\Adapter $adapter The authentication adapter that should be used
     * @param \Zend\Authentication\AuthenticationService $service The service that should be used
     */
	public function __construct(Adapter $adapter, AuthenticationService $service)
	{
		$this->_adapter = $adapter;
		$this->_service = $service;
	}
	
	/**
	 * Authenticate the user.
	 *
	 * @param string $identity The provided identity
     * @param string $credential The provided credential
     * @return void
	 */
	public function authenticate($identity = '', $credential = '')
	{
		if (('' != $identity) && ('' != $credential)) {
			$this->_adapter->setIdentity($identity)->setCredential($credential);
		}
		$this->_result = $this->_service->authenticate($this->_adapter);
	}
	
	/**
	 * Forget the current user.
     *
     * @return void
	 */
	public function forget()
	{
		$this->_service->clearIdentity();
		unset($this->_result);
	}
	
    /**
     * Returns true if the provided user has been authenticated.
     *
     * @return bool
     */
	public function isAuthenticated()
	{
		if (!isset($this->_result))
			return false;
		return $this->_result->isValid();
	}
	
	/**
	 * Return the Doctrine person object.
	 *
	 * @return mixed
	 */
	public function getPersonObject()
	{
		if (!isset($this->_result))
			return null;
		return $this->_result->getPersonObject();
	}
}
