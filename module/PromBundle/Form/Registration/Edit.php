<?php

namespace PromBundle\Form\Registration;

use PromBundle\Entity\Bus\Passenger;

/**
 * 'Login' for new registration
 *
 * @author Mathijs Cuppens
 * @author Kristof Marien <kristof.marien@litus.cc>
 */
class Edit extends \PromBundle\Form\Registration\Add
{
    /**
     * @var Passenger
     */
    private $passenger;

    public function init()
    {
        $firstName = '';
        $lastName = '';
        $email = '';
        $code = '';
        $firstBus = 0;
        $secondBus = 0;
        $firstBusEntity = null;
        $secondBusEntity = null;

        if ($this->passenger !== null) {
            $firstName = $this->passenger->getFirstName();
            $lastName = $this->passenger->getLastName();
            $email = $this->passenger->getEmail();

            $codeEntity = $this->passenger->getCode();
            if ($codeEntity !== null) {
                $code = $codeEntity->getCode();
            }

            $firstBusEntity = $this->passenger->getFirstBus();
            if ($firstBusEntity !== null) {
                $firstBus = $firstBusEntity->getId();
            }

            $secondBusEntity = $this->passenger->getSecondBus();
            if ($secondBusEntity !== null) {
                $secondBus = $secondBusEntity->getId();
            }
        }

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'first_name',
                'label'      => 'First Name',
                'value'      => $firstName,
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'last_name',
                'label'      => 'Last Name',
                'value'      => $lastName,
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'email',
                'label'      => 'Email',
                'value'      => $email,
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'ticket_code',
                'label'      => 'Ticket Code',
                'value'      => $code,
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'first_bus',
                'label'      => 'Go Bus',
                'required'   => true,
                'value'      => $firstBus,
                'attributes' => array(
                    'id'      => 'first_bus',
                    'options' => $this->getFirstBusses(),
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'BusSelected',
                            ),
                            array(
                                'name'    => 'BusSeats',
                                'options' => array(
                                    'bus' => $firstBusEntity,
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
                'name'       => 'second_bus',
                'label'      => 'Return Bus',
                'required'   => true,
                'value'      => $secondBus,
                'attributes' => array(
                    'id'      => 'second_bus',
                    'options' => $this->getSecondBusses(),
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'BusSelected',
                            ),
                            array(
                                'name'    => 'BusSeats',
                                'options' => array(
                                    'bus' => $secondBusEntity,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save Reservation', 'btn btn-default');
    }

    /**
     * @return array
     */
    protected function getFirstBusses()
    {
        $array = parent::getFirstBusses();

        $bus = $this->passenger->getFirstBus();

        if ($bus !== null) {
            // Add one, because you don't want to count in the person himself
            $seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats() + 1;
            $array[$bus->getId()] = $bus->getName() . ' - ' . $bus->getDepartureTime()->format('H:i') . ' (' . $seatsLeft . ' seats left)';
        }

        return $array;
    }

    /**
     * @return array
     */
    protected function getSecondBusses()
    {
        $array = parent::getSecondBusses();

        $bus = $this->passenger->getSecondBus();

        if ($bus !== null) {
            // Add one, because you don't want to count in the person himself
            $seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats() + 1;
            $array[$bus->getId()] = $bus->getName() . ' - ' . $bus->getDepartureTime()->format('H:i') . ' (' . $seatsLeft . ' seats left)';
        }

        return $array;
    }

    /**
     * @param  Passenger $passenger
     * @return self
     */
    public function setPassenger(Passenger $passenger)
    {
        $this->passenger = $passenger;

        return $this;
    }
}
