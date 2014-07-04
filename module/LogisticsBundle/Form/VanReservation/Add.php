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

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    LogisticsBundle\Component\Validator\ReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\VanReservation,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Reservation.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $currentYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Hidden('passenger_id');
        $field->setAttribute('id', 'passengerId');
        $this->add($field);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('class', $field->getAttribute('class') . ' start')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('class', $field->getAttribute('class') . ' end')
            ->setRequired();
        $this->add($field);

        $field = new Text('reason');
        $field->setLabel('Reason')
            ->setAttribute('class', $field->getAttribute('class') . ' reason')
            ->setRequired();
        $this->add($field);

        $field = new Text('load');
        $field->setLabel('Load')
            ->setAttribute('class', $field->getAttribute('class') . ' load');
        $this->add($field);

        $field = new Textarea('additional_info');
        $field->setLabel('Additional Information')
            ->setAttribute('style', 'height:80px;resize:none;')
            ->setAttribute('class', $field->getAttribute('class') . ' additional');
        $this->add($field);

        $field = new Select('driver');
        $field->setLabel('Driver')
            ->setAttribute('options', $this->_populateDriversArray($currentYear))
            ->setAttribute('class', $field->getAttribute('class') . ' driver');
        $this->add($field);

        $field = new Text('passenger');
        $field->setLabel('Passenger')
            ->setAttribute('class', $field->getAttribute('class') . ' passenger')
            ->setAttribute('id', 'passengerSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);

        $field = new Submit('add');
        $field->setValue('Add')
            ->setAttribute('class', 'reservation_add btn btn-primary');
        $this->add($field);

        $field = new Submit('edit');
        $field->setValue('Edit')
            ->setAttribute('class', 'reservation_edit btn btn-primary');
        $this->add($field);
    }

    private function _populateDriversArray(AcademicYear $currentYear)
    {
        $drivers = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($currentYear);

        $driversArray = array(
            -1 => ''
        );
        foreach ($drivers as $driver) {
            $driversArray[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName();
        }

        return $driversArray;
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
            if ($this->data['passenger_id'] == '' && $this->get('passenger')) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'passenger',
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
