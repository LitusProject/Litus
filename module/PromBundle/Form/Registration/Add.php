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
                'options' => array(
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
                'options' => array(
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
                $array[$bus->getId()] =  $bus->getName() . ' - ' . $bus->getDepartureTime()->format('H:i') . ' (' . $seatsLeft . ' seats left)';
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
