<?php

namespace Admin\Form\Users;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;
use \Litus\Validator\IdenticalField as IdenticalFieldValidator;

use \Zend\Form\Form;
use \Zend\Form\Element\Multiselect;
use \Zend\Form\Element\Password;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Registry;

class Add extends \Litus\Form\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/users/add');
        $this->setMethod('post');

        $field = new Text('username');
        $field->setLabel('Username')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Password('credential');
        $field->setLabel('Password')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Password('verify_credential');
        $field->setLabel('Repeat Password')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new IdenticalFieldValidator('credential', 'Password'));
        $this->addElement($field);

        $field = new Multiselect('roles');
        $field->setLabel('Groups')
                ->setMultiOptions($this->_generateRoles())
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('email');
        $field->setLabel('E-mail')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $submit = new Submit('submit');
        $submit->setLabel('Add')
                ->setAttrib('class', 'users_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }

    private function _generateRoles()
    {
        $roles = Registry::get('EntityManager')->getRepository('Litus\Entity\Acl\Role')->findAll();
        $parents = array();
        foreach ($roles as $role) {
            if ('guest' == $role->getName()) {
                continue;
            }
            $parents[$role->getName()] = $role->getName();
        }
        return $parents;
    }
}