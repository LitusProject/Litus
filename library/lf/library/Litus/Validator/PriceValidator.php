<?php

namespace Litus\Validator;

/**
* This validator can be used to check if a given price is valid.
*/
use Zend\Validator\Regex;

class PriceValidator extends Regex
{
    function __construct() {
    	parent::__construct('/^[0-9]+.?[0-9]{0,2}$/');
    	parent::setMessage('The given price is not valid');
    }
}