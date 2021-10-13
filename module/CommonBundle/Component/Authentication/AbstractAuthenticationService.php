<?php

namespace CommonBundle\Component\Authentication;

use CommonBundle\Component\Authentication\Action;
use Laminas\Authentication\Storage\StorageInterface;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Header\SetCookie;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;

/**
 * An authentication service superclass that handles the setting and clearing of the cookie.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class AbstractAuthenticationService extends \Laminas\Authentication\AuthenticationService
{
    /**
     * @var string The name of the cookie
     */
    private $name = '';

    /**
     * @var integer The duration for which the cookie is set
     */
    protected $duration = -1;

    /**
     * @var string The domain of the cookie
     */
    protected $domain = '';

    /**
     * @var boolean Whether the cookie is secure or not
     */
    protected $secure = false;

    /**
     * @var Action The action that should be taken after authentication
     */
    protected $action;

    /**
     * @var Request The request
     */
    protected $request;

    /**
     * @var Cookie The received cookies
     */
    private $cookies;

    /**
     * @var Response The HTTP response
     */
    private $response;

    /**
     * @param StorageInterface $storage  The persistent storage handler
     * @param string           $name     The name of the cookie
     * @param integer          $duration The duration for which the cookie is set
     * @param string           $domain   The domain of the cookie
     * @param boolean          $secure   Whether the cookie is secure or not
     * @param Action           $action   The action that should be taken after authentication
     */
    public function __construct(StorageInterface $storage, $name, $duration, $domain, $secure, Action $action)
    {
        parent::__construct($storage);

        $this->duration = $duration;
        $this->name = $name;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->action = $action;
    }

    /**
     * @param Action $action The action that should be taken after authentication
     *
     * @return self
     */
    public function setAction(Action $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param  Request $request The HTTP request
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->cookies = $request->getCookie() ? $request->getCookie() : null;
        $this->request = $request;

        return $this;
    }

    /**
     * @param  Response $response The HTTP response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Checks whether external sites (e.g. wiki) can access this authentication
     *
     * @return boolean
     */
    public function isExternallyVisible()
    {
        return $this->hasCookie();
    }

    /**
     * Returns the value of the cookie
     *
     * @return string
     */
    protected function getCookie()
    {
        return $this->cookies[$this->name];
    }

    /**
     * Checks whether the cookie has been set.
     *
     * @return boolean
     */
    protected function hasCookie()
    {
        return isset($this->cookies[$this->name]);
    }

    /**
     * Clear the authentication cookie.
     */
    protected function clearCookie()
    {
        if (isset($this->cookies[$this->name])) {
            unset($this->cookies[$this->name]);
        }

        $this->response->getHeaders()->addHeader(
            (new SetCookie())
                ->setName($this->name)
                ->setValue('')
                ->setExpires(0)
                ->setMaxAge(0)
                ->setPath('/')
                ->setDomain($this->domain)
                ->setSecure($this->secure)
        );
    }

    /**
     * Set the authentication cookie.
     *
     * @param string $value The cookie's value
     */
    protected function setCookie($value)
    {
        $this->clearCookie();

        $this->cookies[$this->name] = $value;

        $this->response->getHeaders()->addHeader(
            (new SetCookie())
                ->setName($this->name)
                ->setValue($value)
                ->setExpires(time() + $this->duration)
                ->setMaxAge($this->duration)
                ->setPath('/')
                ->setDomain($this->domain)
                ->setSecure($this->secure)
        );
    }
}
