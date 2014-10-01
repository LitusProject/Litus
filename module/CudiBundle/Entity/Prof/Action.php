<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Prof;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Prof\Action")
 * @ORM\Table(name="cudi.prof_actions")
 */
class Action
{
    /**
     * @var integer The ID of this action
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The entity name
     *
     * @ORM\Column(type="string")
     */
    private $entity;

    /**
     * @var integer The entity id
     *
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;

    /**
     * @var integer The previous entity id
     *
     * @ORM\Column(name="previous_id", type="integer", nullable=true)
     */
    private $previousId;

    /**
     * @var string The action type
     *
     * @ORM\Column(type="string")
     */
    private $action;

    /**
     * @var DateTime The time this action was executed
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var Person The person executed this action
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $person;

    /**
     * @var Person The person completed this action
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="completed_person", referencedColumnName="id")
     */
    private $completedPerson;

    /**
     * @var DateTime The time this action was confirmed
     *
     * @ORM\Column(name="confirm_date", type="datetime", nullable=true)
     */
    private $confirmDate;

    /**
     * @var DateTime The time this action was refused
     *
     * @ORM\Column(name="refuse_date", type="datetime", nullable=true)
     */
    private $refuseDate;

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @param Person  $person     The person executed this action
     * @param string  $entity     The entity name
     * @param integer $entityId   The entity id
     * @param string  $action     The action type
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
        if ('article' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($this->entityId);
        } elseif ('file' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findOneById($this->entityId);
        } elseif ('mapping' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findOneById($this->entityId);
        }
    }

    /**
     * @param integer $entityId
     *
     * @return self
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
        if ('article' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($this->previousId);
        } elseif ('file' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findOneById($this->previousId);
        } elseif ('mapping' == $this->entity) {
            return $this->_entityManager
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findOneById($this->previousId);
        }
    }

    /**
     * @param integer $previousId
     *
     * @return self
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Person
     */
    public function getCompletedPerson()
    {
        return $this->completedPerson;
    }

    /**
     * @return DateTime
     */
    public function getConfirmDate()
    {
        return $this->confirmDate;
    }

    /**
     * @return DateTime
     */
    public function getRefuseDate()
    {
        return $this->refuseDate;
    }

    /**
     * @param Person $completedPerson
     *
     * @return self
     */
    public function setCompleted(Person $completedPerson)
    {
        $this->completedPerson = $completedPerson;
        $this->confirmDate = new DateTime();
        $this->refuseDate = null;

        return $this;
    }

    /**
     * @param Person $completedPerson
     *
     * @return self
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
     * @param EntityManager $entityManager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }
}
