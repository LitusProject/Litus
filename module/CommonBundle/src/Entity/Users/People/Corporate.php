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
	CommonBundle\Entity\Users\Statuses\Corporate as CorporateStatus,
	Doctrine\Common\Collections\ArrayCollection;
	
/**
 * This is a person that represents a contact in a company.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\People\Corporate")
 * @Table(name="users.corporate_people")
 */
class Corporate extends \CommonBundle\Entity\Users\Person
{
    /**
     * @OneToMany(
     * 		targetEntity="CommonBundle\Entity\Users\Statuses\Corporate", mappedBy="person", cascade={"persist"}
     * )
     */
    private $corporateStatuses;

    /**
     * @param string $username The contact's username
     * @param \CommonBundle\Entity\Users\Credential $credential The contact's credential
     * @param array $roles The contact's roles
     * @param string $firstName The contact's first name
     * @param string $lastName The contact's last name
     * @param string $email  The contact's e-mail address
     * @param string $phoneNumber The contact's phone number
     * @param string $sex The contact's sex ('m' or 'f')
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $phoneNumber, $sex)
    {
        parent::__construct($username, $credential, $roles, $firstName, $lastName, $email, $phoneNumber, $sex);

        $this->corporateStatuses = new ArrayCollection();
    }

    /**
     * @param string $name
     * @return \CommonBundle\Entity\Users\People\Corporate
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        if (($name === null) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');
            
        $this->name = $name;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $vatNumber
     * @return \CommonBundle\Entity\Users\People\Corporate
     * @throws \InvalidArgumentException
     */
    public function setVatNumber($vatNumber)
    {
        if (($vatNumber === null) || !is_string($vatNumber))
            throw new \InvalidArgumentException('Invalid VAT number');
            
        $this->vatNumber = $vatNumber;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }
    
    /**
     * Adds a corporate status to the list, if possible.
     *
     * @param \CommonBundle\Entity\Users\Statuses\Corporate $corporateStatus
     * @return \CommonBundle\Entity\Users\People\Corporate
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function addCorporateStatus(CorporateStatus $corporateStatus)
    {
    	if (null === $corporateStatus)
    		throw new \InvalidArgumentException('Invalid status');
    	
    	if (!$this->canHaveCorporateStatus())
    		throw \RuntimeException('The corporate status cannot be set');
    		
    	$this->corporateStatuses->add($corporateStatus);
    	
    	return $this;
    }
    
    /**
     * If this person already has a corporate status for this academic year, a new
     * one cannot be set.
     *
     * @return bool
     */
    public function canHaveCorporateStatus()
    {
    	foreach ($this->corporateStatuses as $corporateStatus) {
    		if (AcademicYear::getShortAcademicYear() == $corporateStatus->getYear())
    			return false;
    	}
    	
    	return true;
    }
}
