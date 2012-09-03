<?php

namespace LogisticsBundle\Controller\Admin;

use \Zend\View\Model\ViewModel;

class TestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
	public function indexAction() {
		echo "test";
		
        return new ViewModel(
            array(
                'installerReady' => true,
            )
        );
	}
}