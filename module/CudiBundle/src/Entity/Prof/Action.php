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

namespace CudiBundle\Entity\Prof;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Prof\Action")
 * @Table(name="cudi.prof_actions")
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
     * @var integer The previous entity id
     *
     * @Column(name="previous_id", type="integer", nullable=true)
     */
    private $previousId;

    /**
     * @var string The action type
     *
     * @Column(type="string")
     */
    private $action;

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
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param \CommonBundle\Entity\Users\Person $person The person executed this action
     * @param string $entity The entity name
     * @param integer $entityId The entity id
     * @param string $action The action type
     * @param integer $previousId The previous entity id
     */
    public function __construct(Person $person, $entity, $entityId, $action, $previousId = null)
    {
        $this->person = $person;
        $this->entity = $entity;
        $this->entityId = $entityId;
        $this->previousId = $previousId;
        $this->action = $action;
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
    public function getEntityName()
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        if ('article' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($this->entityId);
        elseif ('file' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Files\Mapping')
                ->findOneById($this->entityId);
        elseif ('mapping' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                ->findOneById($this->entityId);
    }

    /**
     * @param integer $entityId
     *
     * @return \CudiBundle\Entity\Prof\Action
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return mixed
     */
    public function getPreviousEntity()
    {
        if ('article' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($this->previousId);
        elseif ('file' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Files\Mapping')
                ->findOneById($this->previousId);
        elseif ('mapping' == $this->entity)
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                ->findOneById($this->previousId);
    }

    /**
     * @param integer $previousId
     *
     * @return \CudiBundle\Entity\Prof\Action
     */
    public function setPreviousId($previousId)
    {
        $this->previousId = $previousId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
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
     * @param \CommonBundle\Entity\Users\Person $completedPerson
     *
     * @return \CudiBundle\Entity\Prof\Action
     */
    public function setCompleted(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        $this->confirmDate = new DateTime();
        $this->refuseDate = null;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\Users\Person $completedPerson
     *
     * @return \CudiBundle\Entity\Prof\Action
     */
    public function setRefused(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        $this->refuseDate = new DateTime();
        $this->confirmDate = null;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return ($this->confirmDate !== null);
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

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return \CudiBundle\Entity\Prof\Action
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }
}
