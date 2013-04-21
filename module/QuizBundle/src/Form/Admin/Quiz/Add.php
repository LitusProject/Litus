<?php

namespace QuizBundle\Form\Admin\Quiz;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    LogisticsBundle\Component\Validator\ReservationConflictValidator,
    LogisticsBundle\Entity\Reservation\VanReservation,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new quiz
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \QuizBundle\Entity\Quiz The quiz being added
     */
    protected $quiz = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @var null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setRequired();
        $this->add($field);

        // XXX: Edit role: In form?

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'quiz_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'stringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
