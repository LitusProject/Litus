<?php

namespace Litus\Authentication;

use \Litus\Authentication\Action;
use \Litus\Authentication\Adapter;
use \Litus\Authentication\Result;

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
     * @param \Litus\Authentication\Adapter $adapter The authentication adapter that should be used
     * @param \Zend\Authentication\AuthenticationService $service The service that should be used
     * @param null $namespace The namespace that should be used in the service
     */
	public function __construct(Adapter $adapter, AuthenticationService $service, $namespace = null)
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
	public function authenticate($identity = null, $credential = null)
	{
		if ($identity !== null && $credential !== null) {
			$this->_adapter->setIdentity($identity);
			$this->_adapter->setCredential($credential);
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
		$this->_service->clearCredential();
		$this->_result = null;
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
	 * Return the Doctrine user object.
	 *
	 * @return mixed
	 */
	public function getPersonObject()
	{
		if (!isset($this->_result))
			return null;
		return $this->_result->getUserObject();
	}
}