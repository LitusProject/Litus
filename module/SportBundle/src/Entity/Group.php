<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace SportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a group of friends.
 *
 * @Entity(repositoryClass="SportBundle\Repository\Group")
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
     * @OneToMany(targetEntity="SportBundle\Entity\Runner", mappedBy="group", cascade={"persist"})
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
     * @return \SportBundle\Entity\Group
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
