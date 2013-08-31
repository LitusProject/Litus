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

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    LogisticsBundle\Component\Validator\ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Hidden('player_id');
        $field->setAttribute('id', 'playerId');
        $this->add($field);

        $field = new Text('player');
        $field->setLabel('Player')
            ->setRequired()
            ->setAttribute('id', 'playerSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Textarea('additional_info');
        $field->setLabel('Additional Information');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'reservation_add');
        $this->add($field);
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
                        new ReservationConflictValidator('start_date', 'd/m/Y H:i', PianoReservation::PIANO_RESOURCE_NAME, $this->_entityManager)
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

        if (isset($this->data['player_id'])) {
            if ($this->data['player_id'] == '' && $this->get('player')) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'player',
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
                            'name' => 'player_id',
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
