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
 
namespace ProfBundle\Entity;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action")
 * @Table(name="prof.action")
 */
class Action
{
	/**
	 * @var integer The ID of this action
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var string The entity name
	 *
	 * @Column(type="string")
	 */
	private $entity;
	
	/**
	 * @var integer The entity id
	 *
	 * @Column(name="entity_id", type="integer")
	 */
	private $entityId;
	
    /**
     * @var \DateTime The time this action was executed
     *
     * @Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CommonBundle\Entity\Users\Person The person executed this action
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(referencedColumnName="id")
     */
    private $person;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person completed this action
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="completed_person", referencedColumnName="id")
     */
    private $completedPerson;
    
    /**
     * @var \DateTime The time this action was confirmed
     *
     * @Column(name="confirm_date", type="datetime", nullable=true)
     */
    private $confirmDate;
    
    /**
     * @var \DateTime The time this action was refused
     *
     * @Column(name="refuse_date", type="datetime", nullable=true)
     */
    private $refuseDate;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person The person executed this action
     * @param string $entity The entity name
     * @param integer $entityId The entity id
     */
    public function __construct(Person $person, $entity, $entityId)
    {
    	$this->person = $person;
    	$this->entity = $entity;
    	$this->entityId = $entityId;
    	$this->timestamp = new DateTime();
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    /**
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
    
    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCompletedPerson()
    {
        return $this->completedPerson;
    }
    
    /**
     * @return \DateTime
     */
    public function getConfirmDate()
    {
        return $this->completeDate;
    }
    
    /**
     * @return \DateTime
     */
    public function getRefuseDate()
    {
        return $this->refuseDate;
    }
    
    /**
     * @param \CommonBundle\Entity\Users\Person
     *
     * @return \ProfBundle\Entity\Action
     */
    public function setCompleted(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        $this->completeDate = new DateTime();
        $this->refuseDate = null;
        return $this;
    }
    
    /**
     * @param \CommonBundle\Entity\Users\Person
     *
     * @return \ProfBundle\Entity\Action
     */
    public function setRefused(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        $this->refuseDate = new DateTime();
        $this->completeDate = null;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return ($this->completeDate !== null);
    }
    
    /**
     * @return boolean
     */
    public function isRefused()
    {
        return ($this->refuseDate !== null);
    }
    
    /**
     * @return boolean
     */
    public function isUnCompleted()
    {
        return !$this->isCompleted() && !$this->isRefused();
    }
}
