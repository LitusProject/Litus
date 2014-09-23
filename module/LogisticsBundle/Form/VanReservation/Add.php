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

namespace LogisticsBundle\Form\VanReservation;

use CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    LogisticsBundle\Component\Validator\ReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\VanReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @param VanReservation|null
     */
    protected $reservation;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'passenger_id',
            'attributes' => array(
                'id' => 'passengerId',
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'start_date',
            'label'      => 'Start Date',
            'required'   => true,
            'attributes' => array(
                'class'       => 'start',
                'placeholder' => 'dd/mm/yyyy hh:mm',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'end_date',
            'label'      => 'End Date',
            'required'   => true,
            'attributes' => array(
                'class'       => 'end',
                'placeholder' => 'dd/mm/yyyy hh:mm',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                        new ReservationConflictValidator(
                            'start_date',
                            'd/m/Y H:i',
                            VanReservation::VAN_RESOURCE_NAME,
                            $this->getEntityManager(),
                            null === $this->reservation ? 0 : $this->reservation->getId()
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'reason',
            'label'      => 'Reason',
            'required'   => true,
            'attributes' => array(
                'class' => 'reason',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'load',
            'label'      => 'Load',
            'attributes' => array(
                'class' => 'load',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'additional_info',
            'label'      => 'Additional Info',
            'attributes' => array(
                'class' => 'additional',
                'style' => 'height: 80px; resize: none;'
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'driver',
            'label'      => 'Driver',
            'attributes' => array(
                'class'   => 'driver',
                'options' => $this->getDriversArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'passenger',
            'label'      => 'Passenger',
            'attributes' => array(
                'autocomplete' => 'off',
                'class'        => 'passenger',
                'data-provide' => 'typeahead',
                'id'           => 'passengerSearch',
            ),
        ));

        $this->addSubmit('Add', 'reservation_add btn btn-primary', 'add');
            ->addSubmit('Edit', 'reservation_edit btn btn-primary', 'edit');
    }

    public function setReservation(VanReservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    private function getDriversArray()
    {
        $drivers = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($this->getCurrentAcademicYear());

        $driversArray = array(
            -1 => ''
        );
        foreach ($drivers as $driver) {
            $driversArray[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName();
        }

        return $driversArray;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (isset($this->data['passenger_id']) && '' != $this->data['passenger_id']) {
            $specs['passenger_id'] = array(
                'name' => 'passenger_id',
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new AcademicValidator(
                        $this->getEntityManager(),
                        array(
                            'byId' => true,
                        )
                    ),
                ),
            );
        } else {
            $specs['passenger'] = array(
                'name' => 'passenger',
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new AcademicValidator(
                        $this->getEntityManager(),
                        array(
                            'byId' => false,
                        )
                    ),
                ),
            );
        }

        return $specs;
    }
}
