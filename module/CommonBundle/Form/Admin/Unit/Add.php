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

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('mail');
        $field->setLabel('Mail')
            ->setRequired();
        $this->add($field);

        if (count($this->_createOrganizationsArray()) > 1) {
            $field = new Select('organization');
            $field->setLabel('Organization')
                ->setAttribute('options', $this->_createOrganizationsArray());
            $this->add($field);
        }

        $field = new Select('parent');
        $field->setLabel('Parent')
            ->setAttribute('options', $this->createUnitsArray());
        $this->add($field);

        $field = new Select('roles');
        $field->setLabel('Roles')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createRolesArray())
            ->setAttribute('data-help', 'The roles for the members of this unit.');
        $this->add($field);

        $field = new Select('coordinatorRoles');
        $field->setLabel('Coordinator Roles')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createRolesArray())
            ->setAttribute('data-help', 'The roles for the coordinator of this unit.');
        $this->add($field);

        $field = new Checkbox('displayed');
        $field->setLabel('Displayed')
            ->setAttribute('data-help', 'Flag whether this unit will be displayed on the website.');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'unit_add');
        $this->add($field);
    }

    /**
     * Returns an array that has all the organization, so that one can be selected.
     *
     * @return array
     */
    private function _createOrganizationsArray()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findBy(array(), array('name' => 'ASC'));

        if (empty($organizations))
            throw new \RuntimeException('There needs to be at least one organization before you can add a unit');

        $organizationsArray = array();
        foreach ($organizations as $organization)
            $organizationsArray[$organization->getId()] = $organization->getName();

        return $organizationsArray;
    }

    /**
     * Returns an array that has all the units, so that one can be selected.
     *
     * @return array
     */
    protected function createUnitsArray($exclude = 0)
    {
        $units = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        $unitsArray = array(
            '' => ''
        );
        foreach ($units as $unit) {
            if ($unit->getId() != $exclude)
                $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * roles multiselect.
     *
     * @return array
     */
    private function _createRolesArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new \RuntimeException('There needs to be at least one role before you can add a unit');

        return $rolesArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'mail',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailaddress'),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
