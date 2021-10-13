<?php

namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given value is in a valid price format.
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class Price extends \Laminas\Validator\Regex
{
    public function __construct()
    {
        parent::__construct('/^[0-9]+.?[0-9]{0,2}$/');

        $this->setMessage(
            'The given price is not valid'
        );
    }
}
