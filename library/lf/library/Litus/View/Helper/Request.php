<?php

namespace Litus\View\Helper;

use \Zend\Controller\Front;

class Request extends \Zend\View\Helper\AbstractHelper
{
    
    public function __invoke()
    {
        return Front::getInstance()->getRequest();
    }
}