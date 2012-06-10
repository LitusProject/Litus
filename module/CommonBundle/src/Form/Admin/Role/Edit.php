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
 
namespace CommonBundle\Form\Admin\Role;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Entity\Acl\Role,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit;

/**
 * Edit Role
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Role\Add
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param \CommonBundle\Entity\Users\Role $role The person we're going to modify
	 * @param mixed $opts The form's options
	 */
    public function __construct(EntityManager $entityManager, Role $role, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $this->removeElement('name');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'groups_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populate(
            array(
                'name' => $role->getName(),
                'parents' => $this->_createParentsPopulationArray($role->getParents()),
                'actions' => $this->_createActionsPopulationArray($role->getActions(), $role->getParents())
            )
        );
    }

	/**
	 * Returns an array that is in the right format to populate the parents field.
	 *
	 * @param array $parents The role's parents
	 * @return array
	 */
    private function _createParentsPopulationArray(array $parents)
    {
        $parentsArray = array();
        foreach ($parents as $parent) {
            $parentsArray[] = $parent->getName();
        }
        return $parentsArray;
    }
    
    /**
     * Returns an array that is in the right format to populate the actions field.
     *
     * @param array $actions The role's actions
     * @param array $parents The role's parents
     * @return array
     */
    public function _createActionsPopulationArray(array $actions, array $parents)
    {
    	$actionsArray = array();   	
    	foreach ($parents as $parent) {
    		foreach ($parent->getActions() as $action) {
    			$actionsArray[] = $action->getId();
    		}
    	}
    	
    	foreach ($actions as $action) {
    		$actionsArray[] = $action->getId();
    	}
    	
    	return $actionsArray;
    }
}
