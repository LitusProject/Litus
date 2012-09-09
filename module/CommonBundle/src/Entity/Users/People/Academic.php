<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Users\People;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\Users\Credential,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an academic person, e.g. a student or professor.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Users\People\Academic")
 * @ORM\Table(name="users.people_academic")
 */
class Academic extends \CommonBundle\Entity\Users\Person
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
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\Users\Statuses\University", mappedBy="person", cascade={"persist", "remove"})
     */
    private $universityStatuses;

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

        $this->setUniversityIdentification($universityIdentification);
        $this->universityStatuses = new ArrayCollection();
    }

    /**
     * @param string $personalEmail
     * @return \CommonBundle\Entity\Users\People\Academic
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
     * @return \CommonBundle\Entity\Users\People\Academic
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
     * @return \CommonBundle\Entity\Users\People\Academic
     * @throws \InvalidArgumentException
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
     * @return \CommonBundle\Entity\Users\People\Academic
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
     * @param \CommonBundle\Entity\Users\Statuses\University $universityStatus
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function addUniversityStatus(UniversityStatus $universityStatus)
    {
        $this->universityStatuses->add($universityStatus);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @throws \RuntimeException
     */
    public function getUniversityStatus(AcademicYearEntity $academicYear)
    {
        foreach($this->universityStatuses as $status) {
            if ($status->getAcademicYear() == $academicYear)
                return $status;
        }
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @throws \RuntimeException
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
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function setBirthday(DateTime $birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \CommonBundle\Entity\General\Address $primaryAddress
     * @return \CommonBundle\Entity\Users\People\Academic
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
     * @return \CommonBundle\Entity\Users\People\Academic
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
}
