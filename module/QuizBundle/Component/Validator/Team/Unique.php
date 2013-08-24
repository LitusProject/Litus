<?php

namespace QuizBundle\Component\Validator\Team;

use Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Team;

/**
 * Validates the uniqueness of a team number in a quiz
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Unique extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \QuizBundle\Entity\Quiz The quiz where the team belongs to
     */
    private $_quiz = null;

    /**
     * @var \QuizBundle\Entity\Team The team excluded
     */
    private $_team = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The team number already exists'
    );

    /**
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \QuizBundle\Entity\Quiz $quiz The quiz where the team belongs to
     * @param \QuizBundle\Entity\Team $team The team excluded
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Quiz $quiz, Team $team = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_quiz = $quiz;
        $this->_team = $team;
    }

    public function isValid($value) {
        $this->setValue($value);

        if (!is_numeric($value)) {
            $this->error(self::NOT_VALID);
            return false;
        }

        $teams = $this->_entityManager->getRepository('QuizBundle\Entity\Team')
            ->findBy(
                array(
                    'quiz'=>$this->_quiz->getId(),
                    'number'=>$value
                )
            );

        if (count($teams) == 0 || $teams[0] == $this->_team)
            return true;

        $this->error(self::NOT_VALID);
        return false;
    }
}
