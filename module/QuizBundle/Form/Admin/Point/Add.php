<?php

namespace QuizBundle\Form\Admin\Point;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new point
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     *
     * @var \QuizBundle\Entity\Quiz
     */
    private $_quiz = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Quiz
     * @var null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Quiz $quiz, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_quiz = $quiz;


        $field = new Select('team');
        $field->setLabel('Team');
        $field->setAttribute('options', $this->_getTeams());
        $field->setRequired();
        $this->add($field);

        $field = new Select('round');
        $field->setLabel('Round');
        $field->setAttribute('options', $this->_getRounds());
        $field->setRequired();
        $this->add($field);

        $field = new Text('points');
        $field->setLabel('Points');
        $field->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'quiz_point_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();


        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'team',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'round',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'points',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(false),
                    )
                )
            )
        );

        return $inputFilter;
    }

    private function _getTeams()
    {
        $teams = $this->_entityManager
                ->getRepository('QuizBundle\Entity\Team')
                ->findByQuiz($this->_quiz);
        $teamOptions = array();

        foreach($teams as $team)
            $teamOptions[$team->getId()] = $team->getName();

        return $teamOptions;
    }

    private function _getRounds()
    {
        $rounds = $this->_entityManager
                ->getRepository('QuizBundle\Entity\Round')
                ->findByQuiz($this->_quiz);
        $roundOptions = array();

        foreach($rounds as $round)
            $roundOptions[$round->getId()] = $round->getName();

        return $roundOptions;
    }

}
