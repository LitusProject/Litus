<?php

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a group of friends.
 *
 * @ORM\Entity(repositoryClass="SportBundle\Repository\Group")
 * @ORM\Table(name="sport_groups")
 */
class Group
{
    public static $allMembers = array('one', 'two', 'three', 'four', 'five');

    /**
     * @var integer The ID of this group
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
    private $entityManager;

    /**
     * @var boolean Whether to use this group in the cv book or not.
     *
     * @ORM\Column(name="is_speedy_group", type="boolean",nullable=true)
     */
    private $isSpeedyGroup;

    /**
     * @param AcademicYear $academicYear
     */
    public function __construct(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
        $this->members = new ArrayCollection();
        $this->isSpeedyGroup = 0;
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
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }

    /**
     * @rparam boolean $isSpeedyGroup
     * @return self
     */
    public function setIsSpeedyGroup($isSpeedyGroup)
    {
        $this->isSpeedyGroup = $isSpeedyGroup;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsSpeedyGroup()
    {
        return $this->isSpeedyGroup;
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
        $this->entityManager = $entityManager;

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
            $member->setEntityManager($this->entityManager);

            foreach ($member->getLaps($academicYear) as $lap) {
                if ($lap->getEndTime() === null) {
                    continue;
                }

                $lap->setEntityManager($this->entityManager);

                $startTime = $lap->getStartTime()->format('H');
                $endTime = $lap->getEndTime()->format('H');

                $points += $lap->getPoints();

                $happyHours = $this->getHappyHours();
                for ($i = 0; isset($happyHours[$i]); $i++) {
                    if ($startTime >= substr($happyHours[$i], 0, 2) && $endTime <= substr($happyHours[$i], 2)) {
                        $points += $lap->getPoints();
                        if ($this->getIsSpeedyGroup() && $this->isNightShift($happyHours[$i])) {
                            $points += $lap->getPoints();
                        }
                    }
                }
            }
        }

        return $points;
    }

    public function isNightShift($happyHour)
    {
        if ($happyHour === '0204') {
            return true;
        }
        if ($happyHour === '0406') {
            return true;
        }
        if ($happyHour === '0608') {
            return true;
        }
        return $happyHour === '0810';
    }
}
