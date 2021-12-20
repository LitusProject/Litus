<?php

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Group")
 * @ORM\Table(name="syllabus_groups")
 */
class Group
{
    /**
     * @var integer The ID of the group
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of the group
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var boolean Whether to use this group in the cv book or not.
     *
     * @ORM\Column(name="cv_book", type="boolean")
     */
    private $cvBook;

    /**
     * @var boolean Whether this group is removed or not
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @var string Comma separated string of extra members
     *
     * @ORM\Column(type="text", name="extra_members")
     */
    private $extraMembers;

    /**
     * @var string Comma separated string of excluded members
     *
     * @ORM\Column(type="text", name="excluded_members", nullable=true)
     */
    private $excludedMembers;

    /**
     * @var EntityManager The EntityManager instance
     */
    protected $entityManager;

    public function __construct()
    {
        $this->removed = false;
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
     * @return boolean
     */
    public function getCvBook()
    {
        return $this->cvBook;
    }

    /**
     * @param  boolean $cvBook
     * @return self
     */
    public function setCvBook($cvBook)
    {
        $this->cvBook = $cvBook;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->removed = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtraMembers()
    {
        return $this->extraMembers;
    }

    /**
     * @param  string $extraMembers
     * @return self
     */
    public function setExtraMembers($extraMembers)
    {
        $this->extraMembers = $extraMembers;

        return $this;
    }

    /**
     * @return string
     */
    public function getExcludedMembers()
    {
        return $this->excludedMembers;
    }

    /**
     * @param  string $excludedMembers
     * @return self
     */
    public function setExcludedMembers($excludedMembers)
    {
        $this->excludedMembers = $excludedMembers;

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
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNbStudents(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findNbStudentsByGroupAndAcademicYear($this, $academicYear);
    }

    /**
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNbOfPocers(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->getNbOfPocersFromGroupEntity($this, $academicYear);
    }

    /**
     *returns boolean
     */
    public function getIsPocGroup(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->getIsPocGroup($this, $academicYear);
    }

    /**
     * @param  AcademicYear $academicYear
     * @return string
     */
    public function getEmailAdressPoc(AcademicYear $academicYear)
    {
        $pocIndicator = $this->entityManager
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findIndicatorFromGroupAndAcademicYear($this, $academicYear);

        return $pocIndicator->getEmailAdress();
    }

    /**
     * @param string $email
     * @return self
     */
    public function addToExcluded(string $email) {
        $oldExcluded = unserialize($this->excludedMembers);
        array_push($oldExcluded, $email);
        $this->excludedMembers = serialize($oldExcluded);
        return $this;
    }
}
