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

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * This entity represents a group of friends.
 *
 * @ORM\Entity(repositoryClass="SportBundle\Repository\Group")
 * @ORM\Table(name="sport.groups")
 */
class Group
{
    /**
     * @var int The ID of this group
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var AcademicYear The year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var string The name of this group
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var ArrayCollection The members of this group
     *
     * @ORM\OneToMany(targetEntity="SportBundle\Entity\Runner", mappedBy="group")
     * @ORM\OrderBy({"lastName" = "ASC"})
     */
    private $members;

    /**
     * @var string The happy hours of this group
     *
     * @ORM\Column(name="happy_hours", type="string")
     */
    private $happyHours;

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @param AcademicYear $academicYear
     * @param string       $name
     * @param array        $happyHours
     */
    public function __construct(AcademicYear $academicYear, $name, array $happyHours)
    {
        $this->academicYear = $academicYear;

        $this->name = $name;
        $this->happyHours = serialize($happyHours);
        $this->members = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return array
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }

    /**
     * @return array
     */
    public function getHappyHours()
    {
        return unserialize($this->happyHours);
    }

    /**
     * @param  array $happyHours
     * @return self
     */
    public function setHappyHours(array $happyHours)
    {
        $this->happyHours = serialize($happyHours);

        return $this;
    }

    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }

    /**
     * Returns the current point total of the group.
     *
     * @param  AcademicYear $academicYear The academic year
     * @return integer
     */
    public function getPoints(AcademicYear $academicYear)
    {
        $points = 0;
        foreach ($this->getMembers() as $member) {
            $member->setEntityManager($this->_entityManager);

            foreach ($member->getLaps($academicYear) as $lap) {
                if (null === $lap->getEndTime())
                    continue;

                $lap->setEntityManager($this->_entityManager);

                $startTime = $lap->getStartTime()->format('H');
                $endTime = $lap->getEndTime()->format('H');

                $points += $lap->getPoints();

                $happyHours = $this->getHappyHours();
                for ($i = 0; isset($happyHours[$i]); $i++) {
                    if ($startTime >= substr($happyHours[$i], 0, 2) && $endTime <= substr($happyHours[$i], 2))
                        $points += $lap->getPoints();
                }
            }
        }

        return $points;
    }
}
