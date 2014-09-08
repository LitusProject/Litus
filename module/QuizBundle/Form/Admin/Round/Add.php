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

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator;
use LogicException;
use QuizBundle\Component\Validator\Round\Unique as UniqueRoundValidator;
use QuizBundle\Entity\Round;
use QuizBundle\Entity\Quiz;

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
        if (null === $this->quiz) {
            throw new LogicException('Quiz cannot be null in order to add rounds');
        }

        parent::init();

        $this->add(array(
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
        ));

        $this->add(array(
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
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
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
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                        new UniqueRoundValidator($this->getEntityManager(), $this->quiz, $this->round),
                    ),
                ),
            ),
        ));

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
