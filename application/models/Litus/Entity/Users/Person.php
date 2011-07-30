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
     * @var int The person's unique identifier
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The person's username
     *
     * @Column(type="string", length=50)
     */
    private $username;

    /**
     * @var \Litus\Entity\Users\Credential The person's credential
     *
     * @OneToOne(targetEntity="\Litus\Entity\Users\Credential", cascade={"ALL"}, fetch="LAZY")
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
     * @Column(name="first_name", type="string", length=20)
     */
    private $firstName;

    /**
     * @Column(name="last_name", type="string", length=30)
     */
    private $lastName;

    /**
     * @Column(type="string", length=100)
     */
    private $email;

    /**
     * @Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @param string $username The user's username
     * @param \Litus\Entity\Users\Credential $credential The user's credential
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email  The user's e-mail address
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email)
    {
        $this->username = $username;
        $this->credential = $credential;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;

        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @param int $id
     * @return \Litus\Entity\Users\Person
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $username
     * @return \Litus\Entity\Users\Person
     */
    public function setUsername($username)
    {
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
     * @param \Litus\Entity\Users\Credential $credential
     * @return \Litus\Entity\Users\Person
     */
    public function setCredential(Credential $credential)
    {
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
     * Add the specified roles to the user
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
     * @param string $firstName
     * @return \Litus\Entity\Users\Person
     */
    public function setFirstName($firstName)
    {
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
     * @return \Litus\Entity\Users\Person
     */
    public function setLastName($lastName)
    {
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
     * @param string $email
     * @return \Litus\Entity\Users\Person
     */
    public function setEmail($email)
    {
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
     * @return \Litus\Entity\Users\Person
     */
    public function setAddress($address)
    {
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
     * @param string $telephone
     * @return \Litus\Entity\Users\Person
     */
    public function setTelephone($telephone)
    {
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
}
