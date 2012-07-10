<?php
 
namespace CommonBundle\Entity\Nodes;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Nodes\Node")
 * @Table(name="nodes.nodes")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
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
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \DateTime The time of creation of this node
     *
     * @Column(name="create_time", type="datetime")
     */
    private $createTime;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person who created this node
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="create_person", referencedColumnName="id")
     */
    private $createPerson;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(Person $person)
    {
        $this->createTime = new DateTime();
        $this->createPerson = $person;
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
    public function getCreateTime()
    {
        return $this->createTime;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreatePerson()
    {
        return $this->createPerson;
    }
}