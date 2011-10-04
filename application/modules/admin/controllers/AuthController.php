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
            ->addActionContext('authenticate', 'json')
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
        $this->view->isAuthenticated = $this->getAuthentication()->isAuthenticated();
        
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->view->form = new LoginForm();
        }
    }

    public function logoutAction()
    {
        $this->broker('viewRenderer')->setNoRender();
        $this->getAuthentication()->forget();

        $this->_redirect('login');
    }

	public function authenticateAction()
    {
        $this->_initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['formData'], $formData);
        $authResult = array(
            'result' => false,
            'reason' => ''
        );

        $this->getAuthentication()
            ->authenticate($formData['username'], $formData['password']);
        
        if ($this->getAuthentication()->isAuthenticated()) {
            $authResult['result'] = true;
        } else {
            $authResult['reason'] = 'USERNAME_PASSWORD';
        }

        echo $this->_json->encode($authResult);
    }
}
