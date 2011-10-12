<?php

namespace Admin\Form\User;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Entity\Users\Person;

use \Zend\Form\Element\Submit;

class Edit extends Add
{
    public function __construct(Person $person, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('username');
        $this->removeElement('credential');
        $this->removeElement('verify_credential');
        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'users_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populate(
            array(
                'roles' => $this->_createRolesArray($person->getRoles()),
                'first_name' => $person->getFirstName(),
                'last_name' => $person->getLastName(),
                'email' => $person->getEmail(),
                'sex' => $person->getSex()
            )
        );
    }

    private function _createRolesArray(array $roles)
    {
        $hiddenRoles = array(
            'guest',
            'company'
        );

        $rolesArray = array();
        foreach ($roles as $role) {
            if (in_array($role->getName(), $hiddenRoles))
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }
        return $rolesArray;
    }
}
