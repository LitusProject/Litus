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

namespace PromBundle\Form\Registration;

use PromBundle\Entity\Bus\Passenger;

/**
 * 'Login' for new registration
 *
 * @author Mathijs Cuppens
 * @author Kristof Marien <kristof.marien@litus.cc>
 */
class Edit extends Add
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

        if (null !== $this->passenger) {
            $firstName = $this->passenger->getFirstName();
            $lastName = $this->passenger->getLastName();
            $email = $this->passenger->getEmail();

            $codeEntity = $this->passenger->getCode();
            if (null !== $codeEntity) {
                $code = $codeEntity->getCode();
            }

            $firstBusEntity = $this->passenger->getFirstBus();
            if (null !== $firstBusEntity) {
                $firstBus = $firstBusEntity->getId();
            }

            $secondBusEntity = $this->passenger->getSecondBus();
            if (null !== $secondBusEntity) {
                $secondBus = $secondBusEntity->getId();
            }
        }

        $this->add(array(
            'type'       => 'text',
            'name'       => 'first_name',
            'label'      => 'First Name',
            'value'      => $firstName,
            'attributes' => array(
                'disabled' => true,
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'last_name',
            'label'      => 'Last Name',
            'value'      => $lastName,
            'attributes' => array(
                'disabled' => true,
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'email',
            'label'    => 'Email',
            'value'      => $email,
            'attributes' => array(
                'disabled' => true,
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'ticket_code',
            'label'      => 'Ticket Code',
            'value'      => $code,
            'attributes' => array(
                'disabled' => true,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'first_bus',
            'label'      => 'Go Bus',
            'required'   => true,
            'value'      => $firstBus,
            'attributes' => array(
                'id'      => 'first_bus',
                'options' => $this->getFirstBusses(),
            ),
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'prom_bus_selected',
                        ),
                        array(
                            'name' => 'prom_bus_seats',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'second_bus',
            'label'      => 'Return Bus',
            'required'   => true,
            'value'      => $secondBus,
            'attributes' => array(
                'id'      => 'second_bus',
                'options' => $this->getSecondBusses(),
            ),
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'prom_bus_selected',
                        ),
                        array(
                            'name' => 'prom_bus_seats',
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Save Reservation', 'btn btn-default');
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
            $array[$bus->getId()] = $bus->getDepartureTime()->format('d/m/Y H:i') . ' | ' . $seatsLeft . ' seats left';
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
            $array[$bus->getId()] = $bus->getDepartureTime()->format('d/m/Y H:i') . ' | ' . $seatsLeft . ' seats left';
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
