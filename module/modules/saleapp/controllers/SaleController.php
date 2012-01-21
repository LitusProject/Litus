<?php

namespace SaleApp;

class SaleController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
    	$this->view->sessionId = $this->_getParam("session");
    }
}

