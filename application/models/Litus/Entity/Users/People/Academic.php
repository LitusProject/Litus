<?php

namespace Litus\Entity\Users\People;

use \Doctrine\Common\Collections\ArrayCollection;

use \Litus\Entity\Users\Credential;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\People\Academic")
 * @Table(name="users.people_academics")
 */
class Academic extends \Litus\Entity\Users\Person
{
    /**
     * @Column(name="personal_email", type="string", length=100, nullable=true)
     */
    private $personalEmail;

    /**
     * @Column(name="primary_email", type="string", length=100, nullable=true)
     */
    private $primaryEmail;

    /**
     * @Column(name="university_identification", type="string", length=8, nullable=true)
     */
    private $universityIdentification;

    /**
     * @Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @OneToMany(targetEntity="Litus\Entity\Users\UniversityStatus", mappedBy="person")
     */
    private $universityStatuses;

    /**
     * @OneToMany(targetEntity="Litus\Entity\Users\UnionStatus", mappedBy="person")
     */
    private $unionStatuses;

    /**
     * @param string $username The user's username
     * @param \Litus\Entity\Users\Credential $credential The user's credential
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email  The user's e-mail address
     * @param $sex string The users sex
     * @return \Litus\Entity\Users\People\Academic
     *
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $sex)
    {
        parent::__construct($username, $credential, $roles, $firstName, $lastName, $email, $sex);

        $this->unionStatuses = new ArrayCollection();
        $this->universityStatuses = new ArrayCollection();
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $personalEmail
     * @return \Litus\Entity\Users\People\Academic
     */
    public function setPersonalEmail($personalEmail)
    {
        if (($personalEmail === null) || !filter_var($personalEmail, FILTER_VALIDATE_EMAIL))
            throw new \InvalidArgumentException('Invalid personal e-mail');
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
     * @throws \InvalidArgumentException
     * @param string $primaryEmail
     * @return \Litus\Entity\Users\People\Academic
     */
    public function setPrimaryEmail($primaryEmail)
    {
        if (($primaryEmail === null) || !filter_var($primaryEmail, FILTER_VALIDATE_EMAIL))
            throw new \InvalidArgumentException('Invalid primary e-mail');
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $universityIdentification
     * @return \Litus\Entity\Users\People\Academic
     */
    public function setUniversityIdentification($universityIdentification)
    {
        if (($universityIdentification === null) || !is_string($universityIdentification))
            throw new \InvalidArgumentException('Invalid university identification');
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
     * @throws \InvalidArgumentException
     * @param string $photoPath
     * @return \Litus\Entity\Users\People\Academic
     */
    public function setPhotoPath($photoPath)
    {
        if (($photoPath === null) || !is_string($photoPath))
            throw new \InvalidArgumentException('Invalid photo path');
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
}
