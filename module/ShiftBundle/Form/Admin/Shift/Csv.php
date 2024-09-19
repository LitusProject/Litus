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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Form\Admin\Shift;

use RuntimeException;

/**
 * Add Shift
 *
 */
class Csv extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '10MB';

    protected $hydrator = 'ShiftBundle\Hydrator\Shift';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'edit_roles',
                'label'      => 'Edit Roles',
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $this->createEditRolesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'manager',
                'label'    => 'Manager',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
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

        $rewards_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.rewards_enabled');

        if ($rewards_enabled) {
            $this->add(
                array(
                    'type'  => 'checkbox',
                    'name'  => 'handled_on_event',
                    'label' => 'Payed at event',
                )
            );
        } else {
            $this->add(
                array(
                    'type'       => 'hidden',
                    'name'       => 'handled_on_event',
                    'attributes' => array(
                        'value' => '0',
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'ticket_needed',
                'label' => 'Ticket needed',
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
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Shifts csv',
            //                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'csv',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'shift_csv');
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
            throw new RuntimeException('There needs to be at least one unit before you can add a shift');
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
            ->findAllActive(30);

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
            throw new RuntimeException('There needs to be at least one location before you can add a shift');
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
            throw new RuntimeException('There needs to be at least one role before you can add a page');
        }

        return $rolesArray;
    }

    /**
     * @return array
     */
    private function createRewardArray()
    {
        return unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.reward_numbers')
        );
    }
}
