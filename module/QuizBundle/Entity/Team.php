<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a team.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Team")
 * @ORM\Table(name="quiz_teams")
 */
class Team
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
     * @var string The name of the team
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Quiz The quiz this team belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var integer The number of the team
     *
     * @ORM\Column(type="smallint")
     */
    private $number;

    /**
     * @var ArrayCollection The points scored by this team
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Point", mappedBy="team", cascade={"remove"})
     */
    private $points;

    /**
     * @param Quiz $quiz
     */
    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->points = new ArrayCollection();
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
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPoints()
    {
        return $this->points;
    }
}
