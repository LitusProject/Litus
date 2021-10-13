<?php

namespace QuizBundle\Form\Admin\Round;

use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Round;
use RuntimeException;

/**
 * Add a new round
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'QuizBundle\Hydrator\Round';

    /**
     * @var Quiz|null The quiz the round will belong to
     */
    protected $quiz = null;

    /**
     * @var Round|null The round, if it already exists
     */
    protected $round = null;

    public function init()
    {
        if ($this->quiz === null) {
            throw new RuntimeException('Quiz cannot be null when adding a round');
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
                'name'     => 'max_points',
                'label'    => 'Maximum Points',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                            array('name' => 'PositiveNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'order',
                'label'    => 'Round Number',
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
                                'name'    => 'RoundNumber',
                                'options' => array(
                                    'quiz'  => $this->quiz,
                                    'round' => $this->round,
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
     * @param  Round $round
     * @return self
     */
    public function setRound(Round $round)
    {
        $this->round = $round;

        return $this->setQuiz($round->getQuiz());
    }
}
