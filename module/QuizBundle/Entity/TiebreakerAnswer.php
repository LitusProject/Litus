<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * This is the entity for a tiebreaker answer.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\TiebreakerAnswer")
 * @ORM\Table(name="quiz_tiebreaker_answers",
 *        uniqueConstraints={
 *            @UniqueConstraint(name="tiebreaker_answer_unique",
 *                  columns={"tiebreaker", "team"})
 *    })
 */
class TiebreakerAnswer
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
     * @var Tiebreaker The tiebreaker this tiebreaker answer belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Tiebreaker")
     * @ORM\JoinColumn(name="tiebreaker", referencedColumnName="id", onDelete="CASCADE")
     */
    private $tiebreaker;

    /**
     * @var Team The team this tiebreaker answer belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Team")
     * @ORM\JoinColumn(name="team", referencedColumnName="id")
     */
    private $team;

    /**
     * @var integer The tiebreaker answer
     *
     * @ORM\Column(type="integer")
     */
    private $answer;

    /**
     * @param Tiebreaker $tiebreaker
     * @param Team       $team
     * @param integer    $answer
     */
    public function __construct(Tiebreaker $tiebreaker, Team $team, int $answer)
    {
        $this->tiebreaker = $tiebreaker;
        $this->team = $team;
        $this->setAnswer($answer);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Tiebreaker
     */
    public function getTiebreaker()
    {
        return $this->tiebreaker;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return integer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param integer $answer
     * @return self
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }
}
