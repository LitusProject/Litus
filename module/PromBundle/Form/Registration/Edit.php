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

use CommonBundle\Component\Util\AcademicYear;
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
            'type'       => 'text',
            'name'       => 'email',
            'label'      => 'Email',
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
            'options' => array(
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
            'options' => array(
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
        ));

        $this->addSubmit('Save Reservation', 'btn btn-default');
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
