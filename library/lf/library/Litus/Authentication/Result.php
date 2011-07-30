<?php

namespace Litus\Authentication;

abstract class Result extends \Zend\Authentication\Result {
    /**
     * Return the user object from the Doctrine query.
	 *
	 * @return \Litus\Entity\Users\Person
     */
    public abstract function getPersonObject();
}