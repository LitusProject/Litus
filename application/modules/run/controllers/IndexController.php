<?php

namespace Run;

class IndexController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('index', 'queue');
    }
}

