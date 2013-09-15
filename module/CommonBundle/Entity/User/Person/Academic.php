<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Entity\User\Person;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\User\Credential,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an academic person, e.g. a student or professor.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Academic")
 * @ORM\Table(name="users.people_academic")
 */
class Academic extends \CommonBundle\Entity\User\Person
{
    /**
     * @var string The user's personal email
     *
     * @ORM\Column(name="personal_email", type="string", length=100, nullable=true)
     */
    private $personalEmail;

    /**
     * @var string The user's university email
     *
     * @ORM\Column(name="university_email", type="string", length=100, nullable=true)
     */
    private $universityEmail;

    /**
     * @var string The user's university identification
     *
     * @ORM\Column(name="university_identification", type="string", length=8, nullable=true)
     */
    private $universityIdentification;

    /**
     * @var \DateTime The user's birthday
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string The path to the user's photo
     *
     * @ORM\Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var \CommonBundle\Entity\General\Address The primary address of the academic
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="primary_address", referencedColumnName="id")
     */
    private $primaryAddress;

    /**
     * @var \CommonBundle\Entity\General\Address The secondary address of the academic
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="secondary_address", referencedColumnName="id")
     */
    private $secondaryAddress;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The user's university statuses
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Status\University", mappedBy="person", cascade={"persist", "remove"})
     */
    private $universityStatuses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The user's organization mapping
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Person\Organization\AcademicYearMap", mappedBy="academic", cascade={"persist", "remove"})
     */
    private $organizationMap;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The user's unit mapping
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Person\Organization\UnitMap", mappedBy="academic", cascade={"persist", "remove"})
     */
    private $unitMap;

    /**
     * @param string $username The user's username
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email The user's e-mail address
     * @param string $phoneNumber The user's phone number
     * @param string $sex The user's sex
     * @param string $universityIdentification The user's university identification
     */
    public function __construct($username, array $roles, $firstName, $lastName, $email, $phoneNumber, $sex, $universityIdentification)
    {
        parent::__construct($username, $roles, $firstName, $lastName, $email, $phoneNumber, $sex);

        $this->setPersonalEmail($email);
        $this->setUniversityEmail($email);

        $this->universityIdentification = $universityIdentification;

        $this->universityStatuses = new ArrayCollection();
        $this->organizationMap = new ArrayCollection();
        $this->unitMap = new ArrayCollection();
    }

    /**
     * @param string $personalEmail
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setPersonalEmail($personalEmail)
    {
        $this->personalEmail = $personalEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalEmail()
    {
        return $this->personalEmail;
    }

    /**
     * @param string $universityEmail
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setUniversityEmail($universityEmail)
    {
        $this->universityEmail = $universityEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniversityEmail()
    {
        return $this->universityEmail;
    }

    /**
     * @param string $universityIdentification
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setUniversityIdentification($universityIdentification)
    {
        $this->universityIdentification = $universityIdentification;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }

    /**
     * @param string $photoPath
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * @param \CommonBundle\Entity\User\Status\University $universityStatus
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function addUniversityStatus(UniversityStatus $universityStatus)
    {
        $this->universityStatuses->add($universityStatus);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return \CommonBundle\Entity\User\Status\University
     */
    public function getUniversityStatus(AcademicYearEntity $academicYear)
    {
        foreach($this->universityStatuses as $status) {
            if ($status->getAcademicYear() == $academicYear)
                return $status;
        }

        return null;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return boolean
     */
    public function canHaveUniversityStatus(AcademicYearEntity $academicYear)
    {
        if ($this->universityStatuses->count() >= 1) {
            if ($this->universityStatuses->exists(
                function($key, $value) use ($academicYear) {
                    if ($value->getAcademicYear() == $academicYear)
                        return true;
                }
            )) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \DateTime $birthday
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setBirthday(DateTime $birthday = null)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \CommonBundle\Entity\General\Address $primaryAddress
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setPrimaryAddress(Address $primaryAddress)
    {
        $this->primaryAddress = $primaryAddress;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getPrimaryAddress()
    {
        return $this->primaryAddress;
    }

    /**
     * @param \CommonBundle\Entity\General\Address $secondaryAddress
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function setSecondaryAddress(Address $secondaryAddress)
    {
        $this->secondaryAddress = $secondaryAddress;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getSecondaryAddress()
    {
        return $this->secondaryAddress;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return \CommonBundle\Entity\General\Organization
     */
    public function getOrganization(AcademicYearEntity $academicYear)
    {
        foreach ($this->organizationMap as $map) {
            if ($map->getAcademicYear() == $academicYear)
                return $map->getOrganization();
        }

        return null;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function getUnit(AcademicYearEntity $academicYear)
    {
        foreach ($this->unitMap as $map) {
            if ($map->getAcademicYear() == $academicYear)
                return $map->getUnit();
        }

        return null;
    }

    /**
     * @param boolean $mergeUnitRoles
     * @return array
     */
    public function getRoles($mergeUnitRoles = true)
    {
        return array_merge(
            parent::getRoles(),
            true === $mergeUnitRoles ? $this->getUnitRoles() : array()
        );
    }

    /**
     * Retrieves all the roles from the academic's units for the
     * latest academic year.
     *
     * @return array
     */
    public function getUnitRoles()
    {
        $latestStartDate = null;
        $unitMaps = array();
        foreach ($this->unitMap as $map) {
            $startDate = $map->getAcademicYear()->getStartDate();
            if ($startDate >= $latestStartDate) {
                $latestStartDate = $startDate;
                $unitMaps[] = $map;
            }
        }

        $roles = array();
        foreach ($unitMaps as $unitMap) {
            $roles = array_merge(
                $roles,
                $unitMap->getUnit()->getRoles(),
                $unitMap->isCoordinator() ? $unitMap->getUnit()->getCoordinatorRoles() : array()
            );
        }

        return $roles;
    }
}
