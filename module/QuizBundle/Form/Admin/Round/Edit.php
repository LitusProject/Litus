<?php

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator,
    QuizBundle\Component\Validator\Round\Unique as UniqueRoundValidator,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Round,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz round
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \QuizBundle\Entity\Round $round
     */
    private $_round;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Round $round The quiz round to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Round $round, $name = null)
    {
        parent::__construct($entityManager, $round->getQuiz(), $name);

        $this->_round = $round;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromRound($round);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('order');

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
                        new UniqueRoundValidator($this->_entityManager, $this->_quiz, $this->_round),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
