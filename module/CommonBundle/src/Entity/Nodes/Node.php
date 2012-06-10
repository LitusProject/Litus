<?php
 
namespace CommonBundle\Entity\Nodes;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Nodes\Node")
 * @Table(name="nodes.node")
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
     * @var \DateTime The time of update of this node
     *
     * @Column(name="update_time", type="datetime", nullable=true)
     */
    private $updateTime;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person who updated this node last
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="update_person", referencedColumnName="id")
     */
    private $updatePerson;
    
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
    
    /**
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        if (null === $this->updateTime)
            return $this->createTime;
        return $this->updateTime;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getUpdatePerson()
    {
        if (null === $this->updatePerson)
            return $this->createPerson;
        return $this->updatePerson;
    }
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     *
     * @return \CommonBundle\Entity\Nodes\Node
     */
    public function setUpdatePerson(Person $updatePerson)
    {
        $this->updatePerson = $updatePerson;
        $this->updateTime = new DateTime();
        return $this;
    }
}