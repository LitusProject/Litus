<?php

namespace QuizBundle\Form\Admin\Tiebreaker;

use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Tiebreaker;
use RuntimeException;

/**
 * Add a new tiebreaker
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'QuizBundle\Hydrator\Tiebreaker';

    /**
     * @var Quiz|null The quiz the tiebreaker will belong to
     */
    protected $quiz = null;

    /**
     * @var Tiebreaker|null The tiebreaker, if it already exists
     */
    protected $tiebreaker = null;

    public function init()
    {
        if ($this->quiz === null) {
            throw new RuntimeException('Quiz cannot be null when adding a tiebreaker');
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
                'name'     => 'correct_answer',
                'label'    => 'Correct Answer',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
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
     * @param  Tiebreaker $tiebreaker
     * @return self
     */
    public function setTiebreaker(Tiebreaker $tiebreaker)
    {
        $this->tiebreaker = $tiebreaker;

        return $this->setQuiz($tiebreaker->getQuiz());
    }
}
