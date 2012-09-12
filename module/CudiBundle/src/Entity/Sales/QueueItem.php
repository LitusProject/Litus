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

namespace CudiBundle\Entity\Sales;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\PayDesk,
    CudiBundle\Entity\Sales\Session,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\QueueItem")
 * @ORM\Table(name="cudi.sales_queue_items")
 */
class QueueItem
{
    /**
     * @var integer The ID of the queue item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\Person The person of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \CudiBundle\Entity\Sales\Session The session of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var \CudiBundle\Entity\Sales\PayDesk The pay desk of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\PayDesk")
     * @ORM\JoinColumn(name="pay_desk", referencedColumnName="id")
     */
    private $payDesk;

    /**
     * @var integer The number of the queue item
     *
     * @ORM\Column(type="smallint", name="queue_number")
     */
    private $queueNumber;

    /**
     * @var string The status of the queue item
     *
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @var \DateTime The time the queue item was created
     *
     * @ORM\Column(type="datetime", name="sign_in_time")
     */
    private $signInTime;

    /**
     * @var \DateTime The time there were articles sold to the queue item
     *
     * @ORM\Column(type="datetime", name="sold_time", nullable=true)
     */
    private $soldTime;

    /**
     * @var string The comment of the queue item
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string The pay method of the queue item
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $payMethod;

    /**
     * @var array The possible states of a queue item
     */
    private static $POSSIBLE_STATUSES = array(
        'signed_in' => 'Signed In',
        'collecting' => 'Collecting',
        'collected' => 'Collected',
        'selling' => 'Selling',
        'hold' => 'Hold',
        'canceled' => 'Canceled',
        'sold' => 'Sold',
    );

    /**
     * @var array The possible pay methods of a queue item
     */
    public static $POSSIBLE_PAY_METHODS = array(
        'cash' => 'Cash',
        'bank' => 'Bank Device',
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\Sales\Session $session
     */
    public function __construct(EntityManager $entityManager, Person $person, Session $session)
    {
        $this->person = $person;
        $this->session = $session;
        $this->setStatus('signed_in');

        $this->queueNumber = $entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->getNextQueueNumber($session);
    }

    /**
     * @return boolean
     */
    public static function isValidQueueStatus($status)
    {
        return array_key_exists($status, self::$POSSIBLE_STATUSES);
    }

    /**
     * @return boolean
     */
    public static function isValidPayMethod($payMethod)
    {
        return array_key_exists($payMethod, self::$POSSIBLE_PAY_METHODS);
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
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param \CudiBundle\Entity\Sales\PayDesk $payDesk
     *
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function setPayDesk(PayDesk $payDesk)
    {
        $this->payDesk = $payDesk;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Sales\PayDesk
     */
    public function getPayDesk()
    {
        return $this->payDesk;
    }

    /**
     * @return integer
     */
    public function getQueueNumber()
        {
        return $this->queueNumber;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusReadable()
    {
        return self::$POSSIBLE_STATUSES[$this->status];
    }

    /**
     * @param string $status
     *
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function setStatus($status)
    {
        if (!self::isValidQueueStatus($status))
            throw new \InvalidArgumentException('The queue status is not valid.');

        $this->status = $status;

        if ($status != 'sold' && $status != 'selling')
            $this->payDesk = null;

        switch ($status) {
            case 'signed_in':
                $this->signInTime = new DateTime();
                break;
            case 'sold':
                $this->soldTime = new DateTime();
                break;
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSignInTime()
    {
        return $this->signInTime;
    }

    /**
     * @return \DateTime
     */
    public function getSoldTime()
    {
        return $this->soldTime;
    }

    /**
     * @param string $comment
     *
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $payMethod
     *
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function setPayMethod($payMethod)
    {
        if (!self::isValidPayMethod($payMethod) && $payMethod !== null)
            throw new \InvalidArgumentException('The pay method is not valid.');

        $this->payMethod = $payMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getPayMethod()
    {
        if (!self::isValidPayMethod($this->payMethod))
            return '';
        return self::$POSSIBLE_PAY_METHODS[$this->payMethod];
    }
}
