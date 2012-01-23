<?php

namespace Litus\Validator;

class Year extends \Zend\Validator\Date
{
    function __construct() {
    	parent::__construct('Y');
    	parent::setMessage('The given year is not valid');
    }
}