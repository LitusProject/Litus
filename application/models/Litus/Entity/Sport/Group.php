<?php

namespace Litus\Entity\Sport;

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
     * @var \Doctrine\Common\Collections\ArrayCollection The members of this group
     *
     * @ManyToOne(targetEntity="Litus\Entity\Sport\Runner")
     * @JoinColumn(name="member", referencedColumnName="university_identification")
     */
    private $members;

    /**
     * @param array $members An array containing the members of this group
     */
    public function __construct(array $members)
    {
        $this->members = new ArrayCollection($members);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }
}