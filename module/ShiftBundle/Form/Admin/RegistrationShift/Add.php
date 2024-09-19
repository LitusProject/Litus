<?php

namespace ShiftBundle\Form\Admin\RegistrationShift;

use RuntimeException;

/**
 * Add Shift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShiftBundle\Hydrator\RegistrationShift';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'visible_date',
                'label'    => 'Visible Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'last_date'  => 'signout_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'final_signin_date',
                'label'    => 'Final Signin Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'last_date'  => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'last_date'  => 'signout_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'signout_date',
                'label'    => 'Sign out Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'last_date'  => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'duplicate_hours',
                'label'      => 'Duplicate by Hours',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createDuplicatesArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'duplicate_days',
                'label'      => 'Duplicate by days',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createDuplicatesArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'edit_roles',
                'label'      => 'Edit Roles',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $this->createEditRolesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'nb_registered',
                'label'    => 'Maximum amount of registrations',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'unit',
                'label'      => 'Unit',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createUnitsArray($academic = null),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'is_cudi_timeslot',
                'label' => 'Is Cudi Timeslot',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'ticket_needed',
                'label' => 'Ticket needed',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'members_only',
                'label' => 'Members only',
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'members_visible',
                'label'    => 'Show attendees',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'event',
                'label'      => 'Event',
                'attributes' => array(
                    'options' => $this->createEventsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'location',
                'label'      => 'Location',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createLocationsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'description',
                'label'      => 'Description',
                'required'   => true,
                'attributes' => array(
                    'rows' => 5,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'shift_add');
    }

    /**
     * @return array
     */
    private function createDuplicatesArray()
    {
        $duplications = array();
        for ($i = 1; $i <= 20; $i++) {
            $duplications[$i] = $i;
        }

        return $duplications;
    }

    /**
     * @param $academic
     * @return array
     */
    protected function createUnitsArray($academic): array
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        if (count($units) == 0) {
            throw new RuntimeException('There needs to be at least one unit before you can add a RegistrationShift');
        }

        $unitsArray = array();
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }

    /**
     * @return array
     */
    private function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => '',
        );
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle();
        }

        return $eventsArray;
    }

    /**
     * @return array
     */
    private function createLocationsArray()
    {
        $locations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Location')
            ->findAllActive();

        if (count($locations) == 0) {
            throw new RuntimeException('There needs to be at least one location before you can add a RegistrationShift');
        }

        $locationsArray = array();
        foreach ($locations as $location) {
            $locationsArray[$location->getId()] = $location->getName();
        }

        return $locationsArray;
    }

    /**
     * @return array
     */
    private function createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem()) {
                $rolesArray[$role->getName()] = $role->getName();
            }
        }

        if (count($rolesArray) == 0) {
            throw new RuntimeException('There needs to be at least one role before you can add a RegistrationShift');
        }

        return $rolesArray;
    }
}
