<?php

namespace LogisticsBundle\Form\Catalog\Order;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\Order;

/**
 * The form used to add a new order.
 *
 * @author Robin Wroblowski
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = \LogisticsBundle\Hydrator\Order::class;

    /**
     * @var Academic
     */
    protected Academic $academic;

    /**
     * @var AcademicYear
     */
    protected AcademicYear $academicYear;

    public function init()
    {
        $this->add(
            array(
                'type'          => 'text',
                'name'          => 'name',
                'label'         => 'Name',
                'required'      => true,
                'attributes'    => array(
                    'placeholder'   => 'Galabal IT',
                ),
                'options' => array(
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
                'type'        => 'textarea',
                'name'        => 'location',
                'label'       => 'Location',
                'placeholder' => 'CW-lab',
                'attributes'  => array(
                    'rows'    => 2,
                ),
                'required'    => true,
                'options'     => array(
                    'input'   => array(
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
                'label'    => 'Start date',
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
                'label'    => 'End date',
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

        $units = $this->createUnitsArray($this->academic);
        if ($this->academic->getUnit($this->academicYear)) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'units',
                    'label'      => 'Units which have access',
                    'attributes' => array(
                        'multiple' => true,
                        'options'  => $units,
                        'value'    => $this->academic->getUnit($this->academicYear)->getId(),
                        'style'    => 'height: 150px',
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'  => 'select',
                'name'  => 'transport',
                'label' => 'Transport',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'options'  => Order::$TRANSPORTS,
                    'value'    => 'Self',
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

        $this->addSubmit('Next', 'btn btn-primary', 'submit');
    }

    /**
     * @param Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic): self
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @param AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear): self
    {
        $this->academicYear = $academicYear;

        return $this;
    }
}
