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
 
namespace CommonBundle\Entity\Users;

use CommonBundle\Component\Util\AcademicYear,
	CommonBundle\Entity\Acl\Role,
 	CommonBundle\Entity\Users\Credential,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a person.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Person")
 * @Table(
 *      name="users.people",
 *      uniqueConstraints={@UniqueConstraint(name="person_unique_username", columns={"username"})}
 * )
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "academic"="CommonBundle\Entity\Users\People\Academic",
 *      "corporate"="CommonBundle\Entity\Users\People\Corporate"
 * })
 */
abstract class Person
{
    /**
     * @var string The persons unique identifier
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The persons username
     *
     * @Column(type="string", length=50)
     */
    private $username;

    /**
     * @var \CommonBundle\Entity\Users\Credential The person's credential
     *
     * @OneToOne(targetEntity="CommonBundle\Entity\Users\Credential", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="credential", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection;
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @JoinTable(name="users.people_roles",
     *      joinColumns={@JoinColumn(name="person", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @var string The persons first name
     *
     * @Column(name="first_name", type="string", length=20)
     */
    private $firstName;

    /**
     * @var string The persons last name
     *
     * @Column(name="last_name", type="string", length=30)
     */
    private $lastName;

    /**
     * @var string The users email address.
     *
     * @Column(type="string", length=100)
     */
    private $email;

    /**
     * @var string The users address
     *
     * @Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @var string The persons telephone number
     *
     * @Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string The persons sex ('m' or 'f')
     *
     * @Column(type="string", length=1)
     */
    private $sex;

    /**
     * @var bool Whether or not this can login
     *
     * @Column(name="can_login", type="boolean")
     */
    private $canLogin;
    
    /**
     * @OneToMany(targetEntity="CommonBundle\Entity\Users\Statuses\Union", mappedBy="person")
     */
    private $unionStatuses;

    /**
     * @param string $username The user's username
     * @param \CommonBundle\Entity\Users\Credential $credential The user's credential
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email The user's e-mail address
     * @param string $phoneNumber The user's phone number
     * @param $sex string The users sex ('m' or 'f')
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $phoneNumber, $sex)
    {
        $this->setUsername($username);
        $this->setCredential($credential);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
		$this->setPhoneNumber($phoneNumber);
        $this->setSex($sex);

        $this->canLogin = true;
        
        $this->roles = new ArrayCollection($roles);        
       	$this->unionStatuses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $username
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        if (($username === null) || !is_string($username))
            throw new \InvalidArgumentException('Invalid username');
            
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param \CommonBundle\Entity\Users\Credential $credential
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setCredential(Credential $credential)
    {
        if ($credential === null)
            throw new \InvalidArgumentException('Credential cannot be null');
            
        $this->credential = $credential;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredential()
    {
        return $this->credential->getCredential();
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param string $credential The credential that should be checked
     * @return bool
     */
    public function validateCredential($credential)
    {
        return $this->credential->validateCredential($credential);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add the specified roles to the user.
     *
     * @param array $roles An array containing the roles that should be added
     * @return \CommonBundle\Entity\Users\Person
     */
    public function addRoles(array $roles)
    {
        $this->roles->add($roles);
        return $this;
    }

    /**
     * Removes all the old rules and adds the given roles.
     *
     * @param array $roles An array containing the roles that should be added
     * @return \CommonBundle\Entity\Users\Person
     */
    public function updateRoles(array $roles)
    {
        foreach ($this->roles as $currentRole) {
            if (!in_array($currentRole, $roles))
                $this->roles->removeElement($currentRole);
        }

        foreach ($roles as $newRole) {
            if (!$this->roles->contains($newRole))
                $this->roles->add($newRole);
        }

        return $this;
    }
    
    /**
     * Removes the given role.
     *
     * @param \CommonBundle\Entity\Acl\Role $role The role that should be removed
     * @return \CommonBundle\Entity\Users\Person
     */
    public function removeRole(Role $role)
    {
    	$this->roles->removeElement($role);
    	
    	return $this;
    }

    /**
     * @param string $firstName
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setFirstName($firstName)
    {
        if (($firstName === null) || !is_string($firstName))
            throw new \InvalidArgumentException('Invalid first name');
            
        $this->firstName = $firstName;

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
     * @param string $lastName
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setLastName($lastName)
    {
        if (($lastName === null) || !is_string($lastName))
            throw new \InvalidArgumentException('Invalid last name');
            
        $this->lastName = $lastName;
        
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
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @param string $email
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setEmail($email)
    {
        if (($email === null) || !filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \InvalidArgumentException('Invalid e-mail');
            
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $address
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setAddress($address)
    {
        if ((null === $address) || !is_string($address))
            throw new \InvalidArgumentException('Invalid address');
            
        $this->address = $address;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $phoneNumber
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setPhoneNumber($phoneNumber)
    {
    	if ('' == $phoneNumber)
    		return $this;
    	
        if (
        	(null === $phoneNumber)
        	|| !preg_match('/^\+(?:[0-9] ?){6,14}[0-9]$/', $phoneNumber)
        ) {
            throw new \InvalidArgumentException('Invalid phone number');
        }
            
        $this->phoneNumber = $phoneNumber;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param $sex string The person's sex
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setSex($sex)
    {
        if(($sex !== 'm') && ($sex !== 'f'))
            throw new \InvalidArgumentException('Invalid sex');
            
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return bool
     */
    public function canLogin()
    {
        return $this->canLogin;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function disableLogin()
    {
        $this->canLogin = false;
        return $this;
    }
    
   /**
    * @return boolean
    */
   public function isMember()
   {
   	foreach ($this->unionStatuses as $status) {
   		if (AcademicYear::getShortAcademicYear() == $status->getYear() && 'non_member' != $status->getStatus())
   			return true;
   	}
   	return false;
   }
}
