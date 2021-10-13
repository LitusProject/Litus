<?php

namespace CommonBundle\Component\Validator;

/**
 * Checks whether the given string is a valid year.
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class Year extends \Laminas\Validator\Date
{
    public function __construct()
    {
        parent::__construct('Y');
        $this->setMessage('The given year is not valid');
    }
}
