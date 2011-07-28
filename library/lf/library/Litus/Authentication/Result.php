<?php

namespace Litus\Authentication;

abstract class Result extends \Zend\Authentication\Result {
    /**
     * Return the user object from the Doctrine query.
	 *
	 * @return \Litus\Entity\Users\Person
     */
    public abstract function getPersonObject();

    /**
	 * Return the credential of the authenticated user.
	 *
	 * @return string
	 */
    public abstract function getCredential();
}