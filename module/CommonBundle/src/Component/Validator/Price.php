<?php

namespace Litus\Validator;

class Price extends \Zend\Validator\Regex
{
    function __construct() {
    	parent::__construct('/^[0-9]+.?[0-9]{0,2}$/');
    	parent::setMessage('The given price is not valid');
    }
}