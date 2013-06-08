<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a team.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Team")
 * @ORM\Table(name="quiz.teams")
 */
class Team
{
    /**
     * @var The entry's unique identifier
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
     * @var \QuizBundle\Entity\Quiz The quiz this team belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var int The number of the team
     *
     * @ORM\Column(type="smallint")
     */
    private $number;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The points scored by this team
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Point", mappedBy="team", cascade="remove")
     */
    private $points;

    /**
     * @param \QuizBundle\Entity\Quiz $quiz
     * @param string $name
     * @param integer $order
     */
    public function __construct(Quiz $quiz, $name, $number)
    {
        $this->quiz = $quiz;
        $this->name = $name;
        $this->number = $number;
        $this->points = new ArrayCollection;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \QuizBundle\Entity\Quiz
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
     * @param string $name
     * @return \QuizBundle\Entity\Team
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return \QuizBundle\Entity\Team
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
}