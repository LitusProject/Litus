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

/**
 * 'Login' for new registration
 *
 * @author Mathijs Cuppens
 */
class Edit extends Add
{
	public function init()
    {
		$this->add(array(
		            'type'       => 'text',
		            'name'       => 'first_name',
		            'label'      => 'First Name',
		            'attributes' => array(
                        'disabled' => true,
                    ),
		        ));

        $this->add(array(
		            'type'       => 'text',
		            'name'       => 'last_name',
		            'label'      => 'Last Name',
		            'attributes' => array(
                        'disabled' => true,
                    ),
		        ));

        $this->add(array(
		        	'type'     => 'text',
		            'name'     => 'email',
		            'label'    => 'Email',
		            'attributes' => array(
                        'disabled' => true,
                    ),
		        ));

        $this->add(array(
		            'type'       => 'text',
		            'name'       => 'ticket_code',
		            'label'      => 'Ticket Code',
		            'attributes' => array(
                        'disabled' => true,
                    ),
		        ));

        $this->add(array(
		            'type'       => 'select',
		            'name'       => 'first_bus',
		            'label'      => 'Go Bus',
		            'required'   => true,
                    'attributes' => array(
                        'id'      => 'first_bus',
                        'options' => $this->getFirstBusses(),
                    ),
                ));

        $this->add(array(
		            'type'       => 'select',
		            'name'       => 'second_bus',
		            'label'      => 'Return Bus',
		            'required'   => true,
                    'attributes' => array(
                        'id'      => 'second_bus',
                        'options' => $this->getSecondBusses(),
                    ),
                ));

        $this->addSubmit('Save Reservation', 'btn btn-default');
    }

    private function getFirstBusses()
    {
    	$busses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->getGoBusses();

        $array = array('0' => 'None Selected');
        foreach ($busses as $bus) {
        	$seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats();
            $array[$bus->getId()] = $bus->getDepartureTime()->format('d/m/Y H:i') .' | '. $seatsLeft .' seats left';
        }

    	return $array;
    }

	private function getSecondBusses()
    {
		$busses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->getReturnBusses();

        $array = array('0' => 'None Selected');
        foreach ($busses as $bus) {
        	$seatsLeft = $bus->getTotalSeats() - $bus->getReservedSeats();
            $array[$bus->getId()] = $bus->getDepartureTime()->format('d/m/Y H:i') .' | '. $seatsLeft .' seats left';
        }

    	return $array;
    }
}