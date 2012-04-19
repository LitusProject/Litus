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
 
namespace CommonBundle\Form\Admin\Person;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Entity\Users\Person,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit;

/**
 * EditPerson
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Edit extends \CommonBundle\Form\Admin\Person\Add
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
	 * @param mixed $opts The form's options
	 */
    public function __construct(EntityManager $entityManager, Person $person, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $this->removeElement('username');
        $this->removeElement('credential');
        $this->removeElement('verify_credential');

        $this->populate(
            array(
                'first_name' => $person->getFirstName(),
                'last_name' => $person->getLastName(),
                'email' => $person->getEmail(),
                'telephone' => $person->getPhonenumber(),
                'sex' => $person->getSex(),
                'roles' => $this->_createRolesPopulationArray($person->getRoles()),
            )
        );
    }

	/**
	 * Returns an array that is in the right format to populate the roles field.
	 *
	 * @param array $toles The user's roles
	 * @return array
	 */
    private function _createRolesPopulationArray(array $roles)
    {
        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[] = $role->getName();
        }
        return $rolesArray;
    }
}
