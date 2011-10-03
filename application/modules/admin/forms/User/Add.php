<?php

namespace Admin\Form\User;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Validator\IdenticalField as IdenticalFieldValidator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Form\Form;
use \Zend\Form\Element\Multiselect;
use \Zend\Form\Element\Password;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Registry;
use \Zend\Validator\Alnum as AlnumValidator;
use \Zend\Validator\Alpha as AlphaValidator;
use \Zend\Validator\EmailAddress as EmailAddressValidator;

class Add extends \Litus\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Text('username');
        $field->setLabel('Username')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new AlnumValidator());
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
                ->setMultiOptions($this->_createRolesArray())
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new AlphaValidator());
        $this->addElement($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new AlphaValidator());
        $this->addElement($field);

        $field = new Text('email');
        $field->setLabel('E-mail')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()))
                ->addValidator(new EmailAddressValidator());
        $this->addElement($field);

		$field = new Select('sex');
		$field->setLabel('Sex')
				->setRequired()
				->setMultiOptions(
						array(
							'm' => 'M',
							'f' => 'F'
						)
					)
				->setDecorators(array(new FieldDecorator()));
		$this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'users_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    private function _createRolesArray()
    {
        $hiddenRoles = array(
            'guest',
            'company'
        );

        $roles = Registry::get(DoctrineResource::REGISTRY_KEY)
            ->getRepository('Litus\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if (in_array($role->getName(), $hiddenRoles))
                continue;
            
            $rolesArray[$role->getName()] = $role->getName();
        }
        return $rolesArray;
    }
}