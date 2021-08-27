<?php

namespace LogisticsBundle\Form\VanReservation;

use LogisticsBundle\Entity\Reservation\Van as VanReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Reservation\Van';

    /**
     * @param VanReservation|null
     */
    protected $reservation;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'start_date',
                'label'      => 'Start Date',
                'required'   => true,
                'attributes' => array(
                    'class'       => 'start',
                    'placeholder' => 'dd/mm/yyyy hh:mm',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'end_date',
                'label'      => 'End Date',
                'required'   => true,
                'attributes' => array(
                    'class'       => 'end',
                    'placeholder' => 'dd/mm/yyyy hh:mm',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
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
                'type'       => 'text',
                'name'       => 'reason',
                'label'      => 'Reason',
                'required'   => true,
                'attributes' => array(
                    'class' => 'reason',
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
                'type'       => 'text',
                'name'       => 'load',
                'label'      => 'Load',
                'attributes' => array(
                    'class' => 'load',
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
                'type'       => 'textarea',
                'name'       => 'additional_info',
                'label'      => 'Additional Info',
                'attributes' => array(
                    'class' => 'additional',
                    'style' => 'height: 80px; resize: none;',
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
                'type'       => 'select',
                'name'       => 'driver',
                'label'      => 'Driver',
                'attributes' => array(
                    'class'   => 'driver',
                    'options' => $this->getDriversArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'passenger',
                'label'      => 'Passenger',
                'attributes' => array(
                    'class' => 'passenger',
                ),
                'required' => false,
                'options'  => array(
                    'input' => array(
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
                'name'       => 'car',
                'label'      => 'Car',
                'attributes' => array(
                    'class'   => 'car',
                    'options' => $this->returnYesNoArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'bike',
                'label'      => 'Cargo bike',
                'attributes' => array(
                    'class'   => 'bike',
                    'options' => $this->returnYesNoArray(),
                ),
            )
        );

        $this->addSubmit('Add', 'reservation_add btn btn-primary', 'add')
            ->addSubmit('Edit', 'reservation_edit btn btn-primary', 'edit');

        if ($this->reservation != null) {
            $this->bind($this->reservation);
        }
    }

    public function setReservation(VanReservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    private function returnYesNoArray()
    {
        return array('N', 'Y');
    }

    private function getDriversArray()
    {
        $drivers = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($this->getCurrentAcademicYear(true));

        $driversArray = array(
            -1 => '',
        );
        foreach ($drivers as $driver) {
            $driversArray[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName();
        }

        return $driversArray;
    }
}
