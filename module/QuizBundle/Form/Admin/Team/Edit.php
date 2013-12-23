<?php

namespace QuizBundle\Form\Admin\Team;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator,
    QuizBundle\Component\Validator\Team\Unique as UniqueTeamValidator,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Team,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz team
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \QuizBundle\Entity\Team $team
     */
    private $_team;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Team $team The quiz team to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Team $team, $name = null)
    {
        parent::__construct($entityManager, $team->getQuiz(), $name);

        $this->_team = $team;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromTeam($team);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('number');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'number',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                        new UniqueTeamValidator($this->_entityManager, $this->_quiz, $this->_team),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
