<?php

namespace ApiBundle\Component\Uri;

/**
 * Litus URI handler.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Litus extends \Laminas\Uri\Uri
{
    protected static $validSchemes = array('litus');

    /**
     * User Info part is not used in Litus URIs.
     *
     * @see    Uri::setUserInfo()
     * @param  string $userInfo
     * @return \ApiBundle\Component\Uri\Litus
     */
    public function setUserInfo($userInfo)
    {
        return $this;
    }

    /**
     * Fragment part is not used in Litus URIs.
     *
     * @see    Uri::setFragment()
     * @param  string $fragment
     * @return \ApiBundle\Component\Uri\Litus
     */
    public function setFragment($fragment)
    {
        return $this;
    }
}
