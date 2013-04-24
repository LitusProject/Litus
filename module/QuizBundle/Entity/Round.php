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

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a quiz round.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Round")
 * @ORM\Table(name="quiz.rounds")
 */
class Round
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
     * @var string The name of the round
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \QuizBundle\Entity\Quiz The quiz this round belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz")
     * @ORM\JoinColumn(name="quiz", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var int The order of the round
     *
     * @ORM\Column(name="round_order", type="smallint")
     */
    private $order;

    /**
     * @var int The max points of the round
     *
     * @ORM\Column(name="max_points", type="smallint")
     */
    private $maxPoints;

    /**
     * @param \QuizBundle\Entity\Quiz $quiz
     * @param string $name
     * @param integer $order
     */
    public function __construct(Quiz $quiz, $name, $maxPoints, $order)
    {
        $this->quiz = $quiz;
        $this->name = $name;
        $this->maxPoints = $maxPoints;
        $this->order = $order;
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
     * @return \QuizBundle\Entity\Round
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return \QuizBundle\Entity\Round
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    /**
     * @param int $maxPoints
     * @return \QuizBundle\Entity\Round
     */
    public function setMaxPoints($maxPoints)
    {
        $this->maxPoints = $maxPoints;
        return $this;
    }
}