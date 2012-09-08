<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Form\Admin\Reservation;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    CommonBundle\Entity\General\AcademicYear,
    LogisticsBundle\Component\Validator\AcademicValidator,
    CalendarBundle\Component\Validator\DateCompare as DateCompareValidator,
    LogisticsBundle\Component\Validator\ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\VanReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $currentYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        
        $drivers = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($currentYear);
        
        $drivernames = array();
        // Add the possibility to select no driver (yet)
        $drivernames[-1] = 'Unspecified';
        foreach($drivers as $driver) {
            $drivernames[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName(); 
        }

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired();
        $this->add($field);
        
        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setRequired();
        $this->add($field);
        
        $field = new Text('reason');
        $field->setLabel('Reason')
            ->setRequired();
        $this->add($field);
        
        $field = new Text('load');
        $field->setLabel('Load');
        $this->add($field);
        
        $field = new Textarea('additional_info');
        $field->setLabel('Additional Information');
        $this->add($field);
        
        $field = new Select('driver');
        $field->setLabel('Driver')
            ->setRequired(false)
            ->setAttribute('options', $drivernames);
        $this->add($field);
        
        $field = new Text('passenger_name');
        $field->setLabel('Passenger')
        ->setAttribute('id', 'passengerSearch')
        ->setAttribute('autocomplete', 'off')
        ->setAttribute('data-provide', 'typeahead');
        $this->add($field);
        
        $field = new Hidden('passenger_id');
        $field->setAttribute('id', 'passengerId');
        $this->add($field);
        
        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'reservation_add');
        $this->add($field);
    }
    
    public function populateFromReservation(VanReservation $reservation)
    {
        $driver = $reservation->getDriver();
        
        if (null === $driver) {
            $driverid = -1;
        } else {
            $driverid = $driver->getPerson()->getId();
        }
        
        if (null === $reservation->getPassenger()) {
            $passenger_id = '';
            $passenger_name = '';
        } else {
            $passenger_id = $reservation->getPassenger()->getId();
            $passenger_name = $reservation->getPassenger()->getFullName() . ' - ' . $reservation->getPassenger()->getUniversityIdentification();
        }
        
        $data = array(
            'start_date' => $reservation->getStartDate()->format('d/m/Y H:i'),
            'end_date' => $reservation->getEndDate()->format('d/m/Y H:i'),
            'reason' => $reservation->getReason(),
            'load' => $reservation->getLoad(),
            'additional_info' => $reservation->getAdditionalInfo(),
            'driver' => $driverid,
            'passenger_id' => $passenger_id,
            'passenger_name' => $passenger_name,
        );

        $this->setData($data);
    }
    

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_date',
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
                    ),
                )
            )
        );
        
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
                        new ReservationConflictValidator('start_date', 'd/m/Y H:i', VanReservation::VAN_RESOURCE_NAME, $this->_entityManager)
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'reason',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );
        
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'load',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );
        
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'additional_info',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );
        
        if (isset($this->data['passenger_id'])) {
            if ($this->data['passenger_id'] == '' && $this->get('passenger_name')) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'passenger_name',
                            'required' => false,
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new AcademicValidator(
                                    $this->_entityManager,
                                    array(
                                        'byId' => false,
                                    )
                                )
                            ),
                        )
                    )
                );
            } else {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'passenger_id',
                            'required' => false,
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new AcademicValidator(
                                    $this->_entityManager,
                                    array(
                                        'byId' => true,
                                    )
                                )
                            ),
                        )
                    )
                );
            }
        }
        
        return $inputFilter;
            
    }
}
