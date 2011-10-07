<?php

namespace Litus\Entity\Users;

use \Doctrine\Common\Collections\ArrayCollection;

use \Litus\Entity\Acl\Role;
use \Litus\Entity\Users\Credential;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\Person")
 * @Table(
 *      name="users.people",
 *      uniqueConstraints={@UniqueConstraint(name="person_unique_username", columns={"username"})}
 * )
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "company"="Litus\Entity\Users\People\Company",
 *      "academic"="Litus\Entity\Users\People\Academic"}
 * )
 */
abstract class Person
{
    /**
     * @var int The persons unique identifier
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
     * @var \Litus\Entity\Users\Credential The person's credential
     *
     * @OneToOne(targetEntity="Litus\Entity\Users\Credential", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="credential", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection;
     *
     * @ManyToMany(targetEntity="Litus\Entity\Acl\Role")
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
    private $telephone;

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
     * @param string $username The user's username
     * @param \Litus\Entity\Users\Credential $credential The user's credential
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email  The user's e-mail address
     * @param $sex string The users sex ('m' or 'f')
     * @return \Litus\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $sex)
    {
        $this->setUsername($username);
        $this->setCredential($credential);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);

        $this->setSex($sex);

        $this->roles = new ArrayCollection($roles);
        $this->canLogin = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $username
     * @return \Litus\Entity\Users\Person
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
     * @throws \InvalidArgumentException
     * @param \Litus\Entity\Users\Credential $credential
     * @return \Litus\Entity\Users\Person
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
     * @return \Litus\Entity\Users\Person
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
     * @return \Litus\Entity\Users\Person
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
     * @throws \InvalidArgumentException
     * @param string $firstName
     * @return \Litus\Entity\Users\Person
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
     * @throws \InvalidArgumentException
     * @param string $lastName
     * @return \Litus\Entity\Users\Person
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
     * @throws \InvalidArgumentException
     * @param string $email
     * @return \Litus\Entity\Users\Person
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
     * @throws \InvalidArgumentException
     * @param string $address
     * @return \Litus\Entity\Users\Person
     */
    public function setAddress($address)
    {
        if (($address === null) || !is_string($address))
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
     * @throws \InvalidArgumentException
     * @param string $telephone
     * @return \Litus\Entity\Users\Person
     */
    public function setTelephone($telephone)
    {
        if (($telephone === null) || !filter_var($telephone, FILTER_VALIDATE_INT))
            throw new \InvalidArgumentException('Invalid address');
        $this->telephone = $telephone;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @throws \InvalidArgumentException
     * @param $sex string The person's sex
     * @return \Litus\Entity\Users\Person
     */
    public function setSex($sex)
    {
        if(($sex !== 'm') && ($sex !== 'f'))
            throw new \InvalidArgumentException('Invalid sex: ' . $sex);
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
     * @return \Litus\Entity\Users\Person
     */
    public function disableLogin()
    {
        $this->canLogin = false;
        return $this;
    }
}
