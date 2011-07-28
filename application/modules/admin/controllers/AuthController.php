<?php

namespace Admin;

use \Admin\Form\Auth\Login as LoginForm;
use \Zend\Json\Json;

class AuthController extends \Litus\Controller\Action
{
    private $_json = null;

    public function init()
    {
        parent::init();

        $this->broker('contextSwitch')
                ->addActionContext('dologin', 'json')
                ->setAutoDisableLayout(false)
                ->setAutoJsonSerialization(false)
                ->initContext();
        $this->broker('layout')->disableLayout();

        $this->_json = new Json();
    }

    public function indexAction()
    {
        $this->_forward('login');
    }

    public function loginAction()
    {
        $this->view->form = new LoginForm();
    }

    public function dologinAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest())
            throw new \Litus\Controller\Request\Exception\NoXmlHttpRequestException();
        $this->broker('viewRenderer')->setNoRender();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['formData'], $formData);
        $authResult = array(
            'result' => false,
            'reason' => ''
        );

        $this->getAuthentication()->authenticate($formData['username'], $formData['password']);
        if ($this->getAuthentication()->isAuthenticated()) {
            if($this->hasAccess($this->getAuthentication()->getPersonObject())) {
                $authResult['result'] = true;
            } else {
                $authResult['reason'] = 'PERMISSIONS';
            }
        } else {
            $authResult['reason'] = 'USERNAME_PASSWORD';
        }

        echo $this->_json->encode($authResult);
    }
}
