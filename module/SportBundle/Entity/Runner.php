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
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a runner.
 *
 * @ORM\Entity(repositoryClass="SportBundle\Repository\Runner")
 * @ORM\Table(name="sport.runners")
 */
class Runner
{
    /**
     * @var int The ID of this runner
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The academic linked to this runner
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var string The user's university identification
     *
     * @ORM\Column(name="runner_identification", type="string", length=8, nullable=true, unique=true)
     */
    private $runnerIdentification;

    /**
     * @var string The runner's first name
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string The runner's last name
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var Group The runner's group
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Group", inversedBy="members")
     * @ORM\JoinColumn(name="group_of_friends", referencedColumnName="id")
     */
    private $group;

    /**
     * @var Department The runner's department
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Department", inversedBy="members")
     * @ORM\JoinColumn(name="department", referencedColumnName="id")
     */
    private $department;

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @param string     $firstName
     * @param string     $lastName
     * @param Group      $group
     * @param Department $department
     * @param Academic   $academic
     */
    public function __construct($firstName, $lastName, Academic $academic = null, Group $group = null, Department $department = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->academic = $academic;
        $this->group = $group;
        $this->department = $department;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param  string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param  string $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param  string $runnerIdentification
     * @return self
     */
    public function setRunnerIdentification($runnerIdentification)
    {
        $this->runnerIdentification = $runnerIdentification;

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param  Group $group
     * @return self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param  Department $department
     * @return self
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;

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
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Returns the user's laps.
     *
     * @param  AcademicYear $academicYear The academic year
     * @return array
     */
    public function getLaps(AcademicYear $academicYear)
    {
        return $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findBy(
                array(
                    'runner' => $this->id,
                    'academicYear' => $academicYear
                ),
                array(
                    'registrationTime' => 'ASC'
                )
            );
    }

    /**
     * Returns the current point total of the runner.
     *
     * @param  AcademicYear $academicYear The academic year
     * @return integer
     */
    public function getPoints(AcademicYear $academicYear)
    {
        $points = 0;
        foreach ($this->getLaps($academicYear) as $lap) {
            $lap->setEntityManager($this->_entityManager);
            $points += $lap->getPoints();
        }

        return $points;
    }
}
