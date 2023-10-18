<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a quiz tiebreaker round.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Tiebreaker")
 * @ORM\Table(name="quiz_tiebreakers")
 */
class Tiebreaker
{
    /**
     * @var integer The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the tiebreaker
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Quiz The quiz this tiebreaker belongs to
     *
     * @ORM\OneToOne (targetEntity="QuizBundle\Entity\Quiz", inversedBy="tiebreaker")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var integer The correct answer to this tiebreaker
     *
     * @ORM\Column(name="correct_answer", type="integer")
     */
    private $correctAnswer;

    /**
     * @var ArrayCollection The answers in this tiebreaker
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\TiebreakerAnswer", mappedBy="round", cascade="remove")
     */
    private $answers;

    /**
     * @param Quiz $quiz
     */
    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->quiz->setTiebreaker($this);
        $this->answers = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Quiz
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }

    /**
     * @param int $correctAnswer
     * @return self
     */
    public function setCorrectAnswer(int $correctAnswer)
    {
        $this->correctAnswer = $correctAnswer;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
