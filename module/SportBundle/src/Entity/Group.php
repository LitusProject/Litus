<?php

namespace Litus\Entity\Sport;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Litus\Repository\Sport\Group")
 * @Table(name="sport.groups")
 */
class Group
{
    /**
     * @var int The ID of this group
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this group
     *
     * @Column(type="string", length=50)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The members of this group
     *
     * @OneToMany(targetEntity="Litus\Entity\Sport\Runner", mappedBy="group", cascade={"persist"})
     */
    private $members;

    /**
     * @var array
     *
     * @Column(name="happy_hours", type="string")
     */
    private $happyHours;

    /**
     * @param string $name The name of this group
     * @param array $happyHours An array containing the happy hours of this group
     */

    /**
     * @param string $name The name of this group
     * @param array $happyHours An array containing the happy hours of this group
     */
    public function __construct($name, array $happyHours)
    {
        $this->name = $name;
        $this->happyHours = serialize($happyHours);
    }

    /**
     * @return int
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
     * @param array $members
     * @return \Litus\Entity\Sport\Group
     */
    public function setMembers(array $members)
    {
        $this->members = new ArrayCollection($members);
        return $this;
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }

    /**
     * @return array
     */
    public function getHappyHours()
    {
        return unserialize($this->happyHours);
    }
}
