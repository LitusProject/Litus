<?php

namespace LogisticsBundle\Form\Catalog\Order;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;

/**
 * The form used to add a new Order.
 *
 * @author Robin Wroblowski
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Order';

    /**
     * @var Academic
     */
    protected $academic;

    /**
     * @var AcademicYear
     */
    protected $academicYear;

    public function init()
    {
        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'name',
                'label'   => 'Order Name',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        if ($this->academic->isPraesidium($this->academicYear)) {
            $this->add(
                array(
                    'type'       => 'select',
                    'label'      => 'Unit that has access',
                    'name'       => 'unit',
                    'attributes' => array(
                        'multiple' => true,
                        'options'  => $this->createUnitsArray(),
                        'value'    => $this->academic->getUnit($this->academicYear),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'contact',
                'label'      => 'Contact Name',
                'required'   => true,
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'          => 'contact_name',
                    'placeholder' => 'Contact name',
                    'value'       => $this->academic->getFullName(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'email',
                'label'      => 'Email',
                'required'   => true,
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'          => 'contact_mail',
                    'placeholder' => 'E-mail',
                    'value'       => $this->academic->getEmail(),
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
                'options'    => array(
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
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
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
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
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
                'type'       => 'textarea',
                'name'       => 'description',
                'label'      => 'Description',
                'attributes' => array(
                    'rows' => 3,
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

//        Commented out because this option doesn't work yet
//
//        $this->add(
//            array(
//                'type'  => 'checkbox',
//                'name'  => 'needs_ride',
//                'label' => 'Needs a Van-ride (Kar-rit)',
//            )
//        );

        $this->addSubmit('Next', 'btn btn-primary', 'submit');
    }

    /**
     * @return array
     */
    private function createUnitsArray()
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayedQuery()->getResult();

        $unitsArray = array(
            '' => '',
        );
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }

    /**
     * @return array
     */
    private function createLocationsArray()
    {
        $locations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Location')
            ->findAllActive();

        $locationsArray = array(
            '' => '',
        );
        foreach ($locations as $location) {
            $locationsArray[$location->getId()] = $location->getName();
        }

        return $locationsArray;
    }

    /**
     * @param Academic $academic
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;
    }

    /**
     * @param AcademicYear $academicYear
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
    }
}
