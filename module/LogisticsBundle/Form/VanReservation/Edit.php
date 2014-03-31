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
    CommonBundle\Entity\General\AcademicYear,
    LogisticsBundle\Component\Validator\ReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\VanReservation,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\Factory as InputFactory;

/**
 * This form allows the user to edit the reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\VanReservation\Add
{
    /**
     * @var \LogisticsBundle\\Entity\Reservation\VanReservation
     */
    private $_reservation;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param VanReservation              $reservation
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $currentYear, VanReservation $reservation, $name = null)
    {
        parent::__construct($entityManager, $currentYear, $name);

        $this->_reservation = $reservation;
    }

    public function getInputFilter()
    {
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
                        new ReservationConflictValidator('start_date', 'd/m/Y H:i', VanReservation::VAN_RESOURCE_NAME, $this->_entityManager, $this->_reservation->getId())
                    ),
                )
            )
        );

        return $inputFilter;
    }

}
