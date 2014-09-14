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

use CommonBundle\Entity\General\Organization\Unit,
    RuntimeException;

/**
 * Add Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CommonBundle\Hydrator\General\Organization\Unit';

    /**
     * @var Unit|null
     */
    protected $unit;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'name',
            'label'    => 'Name',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'mail',
            'label'    => 'Mail',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailaddress'),
                    ),
                ),
            ),
        ));

        $organizations = $this->createOrganizationsArray();

        if (count($organizations) > 1) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'organization',
                'label'      => 'Organization',
                'attributes' => array(
                    'options' => $organizations,
                ),
            ));
        }

        $this->add(array(
            'type'       => 'select',
            'name'       => 'parent',
            'label'      => 'Parent',
            'attributes' => array(
                'options' => $this->createUnitsArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'roles',
            'label'      => 'Roles',
            'attributes' => array(
                'data-help' => 'The roles for the members of this unit.',
                'multiple'  => true,
                'options'   => $this->createRolesArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'coordinator_roles',
            'label'      => 'Coordinator Roles',
            'attributes' => array(
                'data-help' => 'The roles for the coordinator of this unit.',
                'multiple'  => true,
                'options'   => $this->createRolesArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'displayed',
            'label'      => 'Displayed',
            'attributes' => array(
                'data-help' => 'Flag whether this unit will be displayed on the website.',
            ),
        ));

        $this->addSubmit('Add', 'unit_add');

        if (null === $this->unit) {
            $this->bind($this->unit);
        }
    }

    /**
     * @param Unit $unit
     * @return self
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Returns an array that has all the organization, so that one can be selected.
     *
     * @return array
     */
    private function createOrganizationsArray()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findBy(array(), array('name' => 'ASC'));

        if (empty($organizations))
            throw new RuntimeException('There needs to be at least one organization before you can add a unit');

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
    protected function createUnitsArray()
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        $exclude = null === $this->unit ? 0 : $unit->getId();

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
    private function createRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new RuntimeException('There needs to be at least one role before you can add a unit');

        return $rolesArray;
    }
}
