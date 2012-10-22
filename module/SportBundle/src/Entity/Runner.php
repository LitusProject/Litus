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

namespace SportBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
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
     * @var \CommonBundle\Entity\General\AcademicYear The year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The academic linked to this runner
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var string The user's university identification
     *
     * @ORM\Column(name="optional_identification", type="string", length=8, nullable=true)
     */
    private $optionalIdentification;

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
     * @var \SportBundle\Entity\Group The runner's group
     *
     * @ORM\ManyToOne(targetEntity="SportBundle\Entity\Group", inversedBy="members")
     * @ORM\JoinColumn(name="group_of_friends", referencedColumnName="id")
     */
    private $group;

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param string $firstName
     * @param string $lastName
     * @param \SportBundle\Entity\Group $group
     * @param \CommonBundle\Entity\Users\People\Academic $academic
     */
    public function __construct(AcademicYear $academicYear, $firstName, $lastName, Group $group = null, Academic $academic = null)
    {
        $this->academicYear = $academicYear;
        $this->academic = $academic;

        $this->firstName = $firstName;
        $this->lastName = $lastName;

        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $academic
     * @return \SportBundle\Entity\Runner
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
     * @param string $firstName
     * @return \SportBundle\Entity\Runner
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
     * @param string $lastName
     * @return \SportBundle\Entity\Runner
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }


    /**
     * @return \SportBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param \SportBundle\Entity\Group $group
     * @return \SportBundle\Entity\Runner
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager Instance
     * @return array
     */
    public function getLaps(EntityManager $entityManager, AcademicYear $academicYear) {
        return $entityManager->getRepository('SportBundle\Entity\Lap')
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
}
