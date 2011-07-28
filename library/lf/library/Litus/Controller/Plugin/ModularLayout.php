<?php

namespace Litus\Controller\Plugin;

use \Zend\Controller\Request\AbstractRequest;
use \Zend\Layout\Layout;

class ModularLayout extends \Zend\Controller\Plugin\AbstractPlugin
{
	public function preDispatch(AbstractRequest $request)
	{
		Layout::getMvcInstance()->setLayout($request->getModuleName());
	}
}
