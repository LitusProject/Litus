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
 *     collection="doorbundle_log",
 *     repositoryClass="DoorBundle\Repository\Log"
 * )
 */
class Log
{
    /**
     * @var integer The ID of this log entry
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var DateTime The timestamp of entry
     *
     * @ODM\Field(type="date")
     */
    private $timestamp;

    /**
     * @var integer The ID of the academic
     *
     * @ODM\Field(type="int")
     */
    private $academic;

    /**
     * @param Academic $academic
     */
    public function __construct(Academic $academic)
    {
        $this->timestamp = new DateTime();
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
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
