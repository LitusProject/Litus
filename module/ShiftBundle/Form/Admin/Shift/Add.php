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

namespace ShiftBundle\Form\Admin\Shift;

use CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    RuntimeException;

/**
 * Add Shift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShiftBundle\Hydrator\Shift';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'manager_id',
            'attributes' => array(
                'id' => 'managerId',
            ),
            'options'    => array(
                'input' => array(
                    'required'   => true,
                    'filters'    => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'start_date',
            'label'    => 'Start Date',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        new DateCompareValidator('now', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'end_date',
            'label'    => 'End Date',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'duplicate_hours',
            'label'      => 'Duplicate by Hours',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createDuplicatesArray(),
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'duplicate_days',
            'label'      => 'Duplicate by days',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createDuplicatesArray(),
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'edit_roles',
            'label'      => 'Edit Roles',
            'attributes' => array(
                'multiple' => true,
                'options'  => $this->createEditRolesArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'manager',
            'label'      => 'Manager',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => 'managerSearch',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'nb_responsibles',
            'label'    => 'Number of Responsibles',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'nb_volunteers',
            'label'    => 'Number of Volunteers',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'unit',
            'label'      => 'Unit',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createUnitsArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'reward',
            'label'      => 'Reward coins',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createRewardArray(),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'handled_on_event',
            'label' => 'Payed at event',
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'event',
            'label'      => 'Event',
            'attributes' => array(
                'options' => $this->createEventsArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'location',
            'label'      => 'Location',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createLocationsArray(),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'name',
            'label'    => 'Name',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'description',
            'label'      => 'Description',
            'required'   => true,
            'attributes' => array(
                'rows' => 5
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'shift_add');
    }

    private function createDuplicatesArray()
    {
        $duplications = array();
        for ($i = 1 ; $i <= 20 ; $i++) {
            $duplications[$i] = $i;
        }

        return $duplications;
    }

    private function createUnitsArray()
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        if (empty($units))
            throw new RuntimeException('There needs to be at least one unit before you can add a shift');

        $unitsArray = array();
        foreach ($units as $unit)
            $unitsArray[$unit->getId()] = $unit->getName();

        return $unitsArray;
    }

    private function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => ''
        );
        foreach ($events as $event)
            $eventsArray[$event->getId()] = $event->getTitle();

        return $eventsArray;
    }

    private function createLocationsArray()
    {
        $locations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Location')
            ->findAllActive();

        if (empty($locations))
            throw new RuntimeException('There needs to be at least one location before you can add a shift');

        $locationsArray = array();
        foreach ($locations as $location)
            $locationsArray[$location->getId()] = $location->getName();

        return $locationsArray;
    }

    private function createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem())
                $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new RuntimeException('There needs to be at least one role before you can add a page');

        return $rolesArray;
    }

    private function createRewardArray()
    {
        return unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.reward_numbers')
        );
    }
}
