<?php

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a group of friends.
 *
 * @ORM\Entity(repositoryClass="SportBundle\Repository\Department")
 * @ORM\Table(name="sport_departments")
 */
class Department
{
    /**
     * @var integer The ID of this department
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this department
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var ArrayCollection The members of this department
     *
     * @ORM\OneToMany(targetEntity="SportBundle\Entity\Runner", mappedBy="department")
     * @ORM\OrderBy({"lastName" = "ASC"})
     */
    private $members;

    /**
     * @var string The happy hours of this department
     *
     * @ORM\Column(name="happy_hours", type="string")
     */
    private $happyHours;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param string   $name
     * @param string[] $happyHours
     */
    public function __construct($name, array $happyHours)
    {
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
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Returns the current point total of the department.
     *
     * @param  AcademicYear $academicYear The academic year
     * @return integer
     */
    public function getPoints(AcademicYear $academicYear)
    {
        $points = 0;

        $laps = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findByAcadmicYearAndDepartment($academicYear, $this);

        foreach ($laps as $lap) {
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
                }
            }
        }

        return $points;
    }
}
