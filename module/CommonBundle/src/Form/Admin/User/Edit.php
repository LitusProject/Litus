<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Form\Admin\User;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Entity\Users\Person;

use \Zend\Form\Element\Submit;

/**
 * Edit a user's data.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
	/**
	 * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
	 * @param mixed $opts The validator's options
	 */
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
                'roles' => $this->_createRolesPopulationArray($person->getRoles()),
                'first_name' => $person->getFirstName(),
                'last_name' => $person->getLastName(),
                'email' => $person->getEmail(),
                'telephone' => $person->getTelephone(),
                'sex' => $person->getSex()
            )
        );
    }

	/**
	 * Returns an array that is in the right format to populate the roles field.
	 *
	 * @return array
	 */
    private function _createRolesPopulationArray(array $roles)
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
