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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use InvalidArgumentException;

/**
 * This is the entity for a point.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Point")
 * @ORM\Table(name="quiz_points",
 *        uniqueConstraints={
 *            @UniqueConstraint(name="point_unique",
 *                  columns={"round", "team"})
 *    })
 */
class Point
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
     * @var Round The round this point belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Round")
     * @ORM\JoinColumn(name="round", referencedColumnName="id")
     */
    private $round;

    /**
     * @var Team The team this point belongs to
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Team")
     * @ORM\JoinColumn(name="team", referencedColumnName="id")
     */
    private $team;

    /**
     * @var integer The point
     *
     * @ORM\Column(type="smallint")
     */
    private $point;

    /**
     * @param Round   $round
     * @param Team    $team
     * @param integer $point
     */
    public function __construct(Round $round, Team $team, $point)
    {
        $this->round = $round;
        $this->team = $team;
        $this->setPoint($point);
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
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param  integer $point
     * @return self
     */
    public function setPoint($point)
    {
        if ($point > $this->round->getMaxPoints()) {
            throw new InvalidArgumentException('Points exceed maximum');
        }
        $this->point = $point;

        return $this;
    }
}
