<?php

namespace SportBundle\Component\Validator;

/**
 * Verifies whether the given value is in a valid university identification format.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class UniversityIdentification extends \Laminas\Validator\Regex
{
    public function __construct()
    {
        parent::__construct('/[rsu][0-9]{7}$/');

        $this->setMessage(
            'The given university identification is not in the correct format of an r-, s-, or u-number'
        );
    }
}
