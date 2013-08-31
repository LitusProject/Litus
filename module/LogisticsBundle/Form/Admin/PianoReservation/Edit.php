<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Form\Admin\PianoReservation;

use CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    LogisticsBundle\Component\Validator\ReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * This form allows the user to edit the reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \LogisticsBundle\Entity\Reservation\PianoReservation
     */
    private $_reservation;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param LogisticsBundle\Entity\Reservation\VanReservation $reservation
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager,PianoReservation $reservation, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_reservation = $reservation;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'reservation_edit');
        $this->add($field);

        $this->_populateFromReservation($reservation);
    }

    private function _populateFromReservation(PianoReservation $reservation)
    {
        $this->setData(
            array(
                'player_id' => $reservation->getPlayer()->getId(),
                'player' => $reservation->getPlayer()->getFullName() . ' - ' . $reservation->getPlayer()->getUniversityIdentification(),
                'start_date' => $reservation->getStartDate()->format('d/m/Y H:i'),
                'end_date' => $reservation->getEndDate()->format('d/m/Y H:i'),
                'additional_info' => $reservation->getAdditionalInfo(),
            )
        );
    }

    public function getInputFilter() {

        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('end_date');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end_date',
                    'required' => true,
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
                        new ReservationConflictValidator('start_date', 'd/m/Y H:i', PianoReservation::PIANO_RESOURCE_NAME, $this->_entityManager, $this->_reservation->getId())
                    ),
                )
            )
        );

        return $inputFilter;
    }

}
