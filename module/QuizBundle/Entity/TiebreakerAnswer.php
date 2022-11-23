<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use InvalidArgumentException;

/**
 * This is the entity for a tiebreaker answer.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\TiebreakerAnswer")
 * @ORM\Table(name="quiz_tiebreaker_answers",
 *        uniqueConstraints={
 *            @UniqueConstraint(name="tiebreaker_answer_unique",
 *                  columns={"round", "team"})
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
     * @var Round The round this tiebreaker answer belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Round")
     * @ORM\JoinColumn(name="round", referencedColumnName="id")
     */
    private $round;

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
     * @ORM\Column(type="smallint")
     */
    private $answer;

    /**
     * @param Round   $round
     * @param Team    $team
     * @param integer $answer
     */
    public function __construct(Round $round, Team $team, $answer)
    {
        $this->round = $round;
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
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
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
     * @param  integer $answer
     * @return self
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }
}
