<?php

namespace QuizBundle\Form\Admin\Team;

use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Team;
use RuntimeException;

/**
 * Add a new team
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'QuizBundle\Hydrator\Team';

    /**
     * @var Quiz|null The quiz the team will belong to
     */
    protected $quiz = null;

    /**
     * @var Team|null The team, if it already exists
     */
    protected $team = null;

    public function init()
    {
        if ($this->quiz === null) {
            throw new RuntimeException('Quiz cannot be null when adding a team');
        }

        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'number',
                'label'    => 'Team Number',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                            ),
                            array(
                                'name' => 'PositiveNumber',
                            ),
                            array(
                                'name'    => 'TeamNumber',
                                'options' => array(
                                    'quiz' => $this->quiz,
                                    'team' => $this->team,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Quiz $quiz
     * @return self
     */
    public function setQuiz(Quiz $quiz)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @param  Team $team
     * @return self
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;

        return $this->setQuiz($team->getQuiz());
    }
}
