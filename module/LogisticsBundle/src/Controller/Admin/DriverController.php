<?php

namespace LogisticsBundle\Controller\Admin;

use \Zend\View\Model\ViewModel;

class DriverController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
	public function manageAction() {
        return new ViewModel();
	}
}