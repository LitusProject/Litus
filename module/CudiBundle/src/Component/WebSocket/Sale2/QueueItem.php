<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Component\WebSocket\Sale2;

use CommonBundle\Component\WebSocket\User,
    Doctrine\ORM\EntityManager;

/**
 * QueueItem Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueItem extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \CudiBundle\Entity\Sales\Session The sale session
     */
    private $_id;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Component\WebSocket\User
     */
    private $_user;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param integer $id The id of the queue item
     */
    public function __construct(EntityManager $entityManager, User $user, $id)
    {
        $this->_entityManager = $entityManager;
        $this->_id = $id;
        $this->_user = $user;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return \CommonBundle\Component\WebSocket\User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return ($item->getStatus() == 'collecting' || $item->getStatus() == 'selling');
    }
}