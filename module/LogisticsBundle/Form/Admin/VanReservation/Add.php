<?php

namespace LogisticsBundle\Form\Admin\VanReservation;

use LogisticsBundle\Entity\Reservation\Van as VanReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Reservation\Van';

    /**
     * @var VanReservation|null
     */
    protected $reservation;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
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
        /**
         * Copy paste this code in the validators above to check for reservation conflicts
         * Was requested to remove by logistics in 2016-2017.
         * array(
                            'name' => 'logistics_reservation_conflict',
                            'options' => array(
                                'start_date' => 'start_date',
                                'format' => 'd/m/Y H:i',
                                'resource' => VanReservation::VAN_RESOURCE_NAME,
                                'reservation_id' => null === $this->reservation ? 0 : $this->reservation->getId(),
                            ),
                        ),
         */

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'reason',
                'label'    => 'Reason',
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
                'type'    => 'text',
                'name'    => 'load',
                'label'   => 'Load',
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
                'type'    => 'textarea',
                'name'    => 'additional_info',
                'label'   => 'Additional Info',
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
                    'options' => $this->getDriversArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'passenger',
                'label'    => 'Passenger',
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

        $this->addSubmit('Add', 'reservation_add');

        if ($this->reservation !== null) {
            $this->bind($this->reservation);
        }
    }

    /**
     * @param  VanReservation $reservation
     * @return self
     */
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
            -1 => '',
        );
        foreach ($drivers as $driver) {
            $driversArray[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName();
        }

        return $driversArray;
    }

    private function returnYesNoArray()
    {
        return array('N','Y');
    }
}
