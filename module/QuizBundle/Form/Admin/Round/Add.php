<?php

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator,
    QuizBundle\Component\Validator\Round\Unique as UniqueRoundValidator,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Round,
    QuizBundle\Entity\Quiz,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new round
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \QuizBundle\Entity\Quiz The quiz the round will belong to
     */
    protected $_quiz = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Quiz $quiz
     * @var null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Quiz $quiz, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_quiz = $quiz;


        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('max_points');
        $field->setLabel('Maximum points')
            ->setRequired();
        $this->add($field);

        $field = new Text('order');
        $field->setLabel('Round number')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    /**
     * Populates the form with values from the entity
     *
     * @param \QuizBundle\Entity\Round $round
     */
    public function populateFromRound(Round $round)
    {
        $this->setData(
            array(
                'name' => $round->getName(),
                'max_points' => $round->getMaxPoints(),
                'order' => $round->getOrder(),
            )
        );
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
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'max_points',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'order',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                        new UniqueRoundValidator($this->_entityManager, $this->_quiz),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
