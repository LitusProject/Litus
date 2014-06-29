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

namespace DoorBundle\Document;

use CommonBundle\Entity\User\Person\Academic,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This document represents an access rule for our door.
 *
 * @ODM\Document(
 *     collection="doorbundle_rules",
 *     repositoryClass="DoorBundle\Repository\Rule"
 * )
 */
class Rule
{
    /**
     * @var integer The ID of this rule
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var DateTime The start date of the rule
     *
     * @ODM\Field(type="date")
     */
    private $startDate;

    /**
     * @var DateTime The end date of the rule
     *
     * @ODM\Field(type="date")
     */
    private $endDate;

    /**
     * @var int The time from when access is allowed
     *
     * @ODM\Field(type="int")
     */
    private $startTime;

    /**
     * @var int The time until when access is allowed
     *
     * @ODM\Field(type="int")
     */
    private $endTime;

    /**
     * @var int The ID of the academic
     *
     * @ODM\Field(type="int")
     */
    private $academic;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $startTime
     * @param int      $endTime
     * @param Academic $academic
     */
    public function __construct(DateTime $startDate, DateTime $endDate, $startTime, $endTime, Academic $academic)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->academic = $academic->getId();
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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param  int  $startTime
     * @return self
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param  int  $endTime
     * @return self
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->academic = $academic->getId();

        return $this
    }

    /**
     * @param  EntityManager $entityManager
     * @return Academic
     */
    public function getAcademic(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->academic);
    }
}
