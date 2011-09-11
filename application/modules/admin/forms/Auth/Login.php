<?php

namespace Admin\Form\Auth;

use \Zend\Form\Form;
use \Zend\Form\Element\Password;
use \Zend\Form\Element\Text;

class Login extends \Zend\Form\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAttrib('id', 'login')
            ->setAction('/admin/auth/dologin')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'form'));

        $username = new Text('username');
        $username->setAttrib('placeholder', 'username')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($username);

        $password = new Password('password');
        $password->setAttrib('placeholder', 'password')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($password);
    }
}