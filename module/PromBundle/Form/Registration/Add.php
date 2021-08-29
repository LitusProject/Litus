<?php

namespace PromBundle\Form\Registration;

use PromBundle\Entity\Bus\ReservationCode;

/**
 * add new registration
 *
 * @author Mathijs Cuppens
 * @author Kristof Marien <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var ReservationCode
     */
    private $code;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'first_name',
                'label'    => 'First  Name',
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
                'type'     => 'text',
                'name'     => 'last_name',
                'label'    => 'Last  Name',
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
                'type'     => 'text',
                'name'     => 'email',
                'label'    => 'Email',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                            array(
                                'name' => 'PassengerExists',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'ticket_code',
                'label'      => 'Ticket Code',
                'value'      => $this->code !== null ? $this->code->getCode() : '',
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'first_bus',
                'label'      => 'Departure Bus',
                'required'   => true,
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
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Reserve Seats', 'btn btn-default');
    }

    /**
     * @return array
     */
    protected function getFirstBusses()
    {
        $busses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->getGoBusses($this->getCurrentAcademicYear());

        $array = array('0' => 'None Selected');
        foreach ($busses as $bus) {
            $seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats();
            if ($seatsLeft > 0) {
                $array[$bus->getId()] = $bus->getName() . ' - ' . $bus->getDepartureTime()->format('H:i') . ' (' . $seatsLeft . ' seats left)';
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    protected function getSecondBusses()
    {
        $busses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->getReturnBusses($this->getCurrentAcademicYear());

        $array = array('0' => 'None Selected');
        foreach ($busses as $bus) {
            $seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats();
            if ($seatsLeft > 0) {
                $array[$bus->getId()] = $bus->getName() . ' - ' . $bus->getDepartureTime()->format('H:i') . ' (' . $seatsLeft . ' seats left)';
            }
        }

        return $array;
    }

    /**
     * @param  ReservationCode $code
     * @return self
     */
    public function setCode(ReservationCode $code)
    {
        $this->code = $code;

        return $this;
    }
}
