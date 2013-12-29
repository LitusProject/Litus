<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Component\Authentication;


use CommonBundle\Component\Authentication\Action,
    Zend\Authentication\Storage\StorageInterface as StorageInterface;

/**
 * An authentication service superclass that handles the setting and clearing of the cookie.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class AbstractAuthenticationService extends \Zend\Authentication\AuthenticationService
{
    /**
     * @var string The namespace the storage handlers will use
     */
    private $_namespace = '';

    /**
     * @var string The name of the cookie
     */
    private $_cookie = '';

    /**
     * @var int The duration of the authentication
     */
    protected $_duration = -1;

    /**
     * @var \CommonBundle\Component\Authentication\Action The action that should be taken after authentication
     */
    protected $_action;

    /**
     * @param \Zend\Authentication\Storage\StorageInterface $storage The persistent storage handler
     * @param string $namespace The namespace the storage handlers will use
     * @param string $cookieSuffix The cookie suffix that is used to store the session cookie
     * @param int $duration The expiration time for the cookie
     * @param \CommonBundle\Component\Authentication\Action $action The action that should be taken after authentication
     */
    public function __construct(StorageInterface $storage, $namespace, $cookieSuffix, $duration, Action $action
    )
    {
        parent::__construct($storage);

        $this->_namespace = $namespace;
        $this->_duration = $duration;
        $this->_cookie = $namespace . '_' . $cookieSuffix;
        $this->_action = $action;
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
     * Checks whether external sites (e.g. wiki) can access this authentication
     *
     * @return bool
     */
    public function isExternallyVisible()
    {
        return $this->_hasCookie();
    }

    /**
     * Returns the value of the cookie
     *
     * @return string
     */
    protected function _getCookie()
    {
        return $_COOKIE[$this->_cookie];
    }

    /**
     * Checks whether the cookie has been set.
     *
     * @return bool
     */
    protected function _hasCookie()
    {
        return isset($_COOKIE[$this->_cookie]);
    }

    /**
     * Clear the authentication cookie.
     */
    protected function _clearCookie()
    {
        unset($_COOKIE[$this->_cookie]);
        setcookie(
            $this->_cookie,
            '',
            -1,
            '/'
        );
    }

    /**
     * Set the authentication cookie.
     *
     * @param string $value The cookie's value
     * @param int $expire The cookie's expiration time
     */
    protected function _setCookie($value)
    {
        $this->_clearCookie();

        $_COOKIE[$this->_cookie] = $value;
        setcookie(
            $this->_cookie,
            $value,
            time() + $this->_duration,
            '/',
            str_replace(array('www.', ','), '', $_SERVER['SERVER_NAME'])
        );
    }
}