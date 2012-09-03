<?php

use \Zend\View\Model\ViewModel;

class TestController extends \CommonBundle\Component\Controller\ActionController\CommonController
{
	public function indexAction() {
		echo "test";
		
        return new ViewModel();
	}
}