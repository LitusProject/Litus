<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace DoorBundle\Document;

use CommonBundle\Entity\User\Person\Academic,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity represents an access rule for our door.
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
     * @var string The time of entry
     *
     * @ODM\Field(type="date")
     */
    private $time;

    /**
     * @var integer The ID of the academic
     *
     * @ODM\Field(type="int")
     */
    private $academic;

    /**
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     */
    public function __construct(Academic $academic)
    {
        $this->time = new DateTime();
        $this->academic = $academic->getId();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return \CommonBundle\Entity\Acl\Role
     */
    public function getAcademic(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->academic);
    }
}
