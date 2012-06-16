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
	CommonBundle\Entity\Users\Credential,
	CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for an academic person, e.g. a student or professor.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\People\Academic")
 * @Table(name="users.academic_people")
 */
class Academic extends \CommonBundle\Entity\Users\Person
{
    /**
     * @var string The user's personal email
     *
     * @Column(name="personal_email", type="string", length=100, nullable=true)
     */
    private $personalEmail;

    /**
     * @var string The user's primary email
     *
     * @Column(name="primary_email", type="string", length=100, nullable=true)
     */
    private $primaryEmail;

    /**
     * @var string The user's university identification
     *
     * @Column(name="university_identification", type="string", length=8, nullable=true)
     */
    private $universityIdentification;

    /**
     * @var string The path to the user's photo
     *
     * @Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The user's university statuses
     *
     * @OneToMany(targetEntity="CommonBundle\Entity\Users\Statuses\University", mappedBy="person", cascade={"persist"})
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
     * @throws \InvalidArgumentException
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
     * @param string $primaryEmail
     * @return \CommonBundle\Entity\Users\People\Academic
     * @throws \InvalidArgumentException
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
     * @param string $universityIdentification
     * @return \CommonBundle\Entity\Users\People\Academic
     * @throws \InvalidArgumentException
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
     * @param string $photoPath
     * @return \CommonBundle\Entity\Users\People\Academic
     * @throws \InvalidArgumentException
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
     * @param \CommonBundle\Entity\Users\Statuses\University $universityStatus
     * @throws \RuntimeException
     */
    public function canHaveUniversityStatus()
    {
    	if ($this->universityStatuses->count() > 1) {
	    	if ($this->universityStatuses->exists(
	    		function($key, $value) {
	    			if ($value->getYear() == AcademicYear::getShortAcademicYear())
	    				return true;
	    		}
	    	)) {
	    		return false;
	    	}
	    }
	    
	    return true;
    }
}
