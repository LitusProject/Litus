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

namespace QuizBundle\Form\Admin\Team;

use CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator;
use LogicException;
use QuizBundle\Component\Validator\Team\Unique as UniqueTeamValidator;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Team;

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
        if (null === $this->quiz) {
            throw new LogicException('Quiz cannot be null in order to add teams');
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
            'name'     => 'number',
            'label'    => 'Team Number',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                        new UniqueTeamValidator($this->getEntityManager(), $this->quiz, $this->team),
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
     * @param  Team $team
     * @return self
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;

        return $this->setQuiz($team->getQuiz());
    }
}
