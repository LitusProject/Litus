<?php

namespace CommonBundle\Component\Authentication;

/**
 * Abstract class extending the basic Zend authentication to make sure a
 * person object can always be requested.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Result extends \Laminas\Authentication\Result
{
    /**
     * Return the user object.
     *
     * @return \CommonBundle\Entity\User\Person
     */
    abstract public function getPersonObject();

    /**
     * Return the session object.
     *
     * @return \CommonBundle\Entity\User\Session
     */
    abstract public function getSessionObject();
}
