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
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "article_add"="ProfBundle\Entity\Action\Article\Add",
 *      "article_edit"="ProfBundle\Entity\Action\Article\Edit",
 *      "mapping_add"="ProfBundle\Entity\Action\Mapping\Add",
 *      "mapping_remove"="ProfBundle\Entity\Action\Mapping\Remove",
 *      "file_add"="ProfBundle\Entity\Action\File\Add",
 *      "file_remove"="ProfBundle\Entity\Action\File\Remove",
 * })
 */
abstract class Action
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
     * @var \CommonBundle\Entity\Users\Person The person executed this action
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(referencedColumnName="id")
     */
    private $person;

    /**
     * @var \DateTime The time this action was executed
     *
     * @Column(name="create_time", type="datetime")
     */
    private $createTime;
    
    /**
     * @var \DateTime The time this action was completed
     *
     * @Column(name="complete_time", type="datetime", nullable=true)
     */
    private $completeTime;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person completed this action
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="completed_person", referencedColumnName="id")
     */
    private $completedPerson;
    
    /**
     * @var \DateTime The time this action was completed
     *
     * @Column(name="refuse_time", type="datetime", nullable=true)
     */
    private $refuseTime;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(Person $person)
    {
    	$this->person = $person;
    	$this->createTime = new DateTime();
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
    
    /**
     * @return \DateTime
     */
    public function getCompleteTime()
    {
        return $this->completeTime;
    }
    
    /**
     * @return \ProfBundle\Entity\Action
     */
    public function setCompleted()
    {
        $this->completeTime = new DateTime();
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return ($this->completeTime !== null);
    }
    
    /**
     * @return boolean
     */
    public function isRefused()
    {
        return ($this->refuseTime !== null);
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCompletePerson()
    {
        return $this->completedPerson;
    }
    
    /**
     * @param \CommonBundle\Entity\Users\Person
     *
     * @return \ProfBundle\Entity\Action
     */
    public function setCompletedPerson(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        return $this;
    }
    
    /**
     * @return string
     */
    abstract function getEntity();
    
    /**
     * @return string
     */
    abstract function getAction();
}
