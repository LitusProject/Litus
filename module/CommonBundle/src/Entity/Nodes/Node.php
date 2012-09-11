<?php

namespace CommonBundle\Entity\Nodes;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Nodes\Node")
 * @ORM\Table(name="nodes.nodes")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "page"="PageBundle\Entity\Nodes\Page",
 *      "news"="NewsBundle\Entity\Nodes\News",
 *      "event"="CalendarBundle\Entity\Nodes\Event"
 * })
 */
abstract class Node
{
    /**
     * @var int The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \CommonBundle\Entity\Users\Person The person who created this node
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @param \CommonBundle\Entity\Users\Person $creationPerson
     */
    public function __construct(Person $creationPerson)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $creationPerson;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }
}
