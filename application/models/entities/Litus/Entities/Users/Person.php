<?php

namespace Litus\Entities\Users;

use \Litus\Entities\Users\Credential;

/**
 * @Entity
 * @Table(name="users.people", uniqueConstraints={@UniqueConstraint(name="unique_username", columns={"username"})})
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({"company"="Litus\Entities\Users\People\Company", "academic"="Litus\Entities\Users\People\Academic"})
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
     * @Column(type="string")
     */
    private $username;

    /**
     * @var \Litus\Entities\Users\Credential The person's credential.
     *
     * @OneToOne(targetEntity="\Litus\Entities\Users\Credential", cascade={"ALL"}, fetch="LAZY")
     */
    private $credential;

    /**
     * @Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @Column(type="string")
     */
    private $address;

    /**
     * @Column(type="string")
     */
    private $telephone;

    /**
     * @Column(type="string")
     */
    private $email;

    /**
     * @ManyToMany(targetEntity="Litus\Entities\Users\UniversityStatus", mappedBy="user", cascade={"ALL"}, fetch="LAZY")
     */
    private $universityStatuses;

    /**
     * @OneToMany(targetEntity="Litus\Entities\Users\UnionStatus", mappedBy="user", cascade={"ALL"}, fetch="LAZY")
     */
    private $unionStatuses;

    public function __construct()
    {
        $this->unionStatuses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->universityStatuses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return bool
     */
    public function canHaveUnionStatus()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canHaveUniversityStatus()
    {
        return true;
    }

    /**
     * Return this person's credential.
     *
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
}
