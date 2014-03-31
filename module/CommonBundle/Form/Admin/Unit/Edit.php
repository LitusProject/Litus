<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Admin\Unit;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Entity\General\Organization\Unit,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager                    $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\Organization\Unit $unit          The unit we're going to modify
     * @param null|string|int                                $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Unit $unit, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Select('parent');
        $field->setLabel('Parent')
            ->setAttribute('options', $this->createUnitsArray($unit->getId()));
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'unit_edit');
        $this->add($field);

        $this->_populateFromUnit($unit);
    }

    private function _populateFromUnit(Unit $unit)
    {
        $data = array(
            'name' => $unit->getName(),
            'mail' => $unit->getMail(),
            'organization' => $unit->getOrganization()->getId(),
            'parent' => null === $unit->getParent() ? '' : $unit->getParent()->getId(),
            'roles' => $this->_createRolesPopulationArray($unit->getRoles(false)),
            'coordinatorRoles' => $this->_createRolesPopulationArray($unit->getCoordinatorRoles(false)),
            'displayed' => $unit->getDisplayed()
        );

        $this->setData($data);
    }

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
