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
 
namespace CudiBundle\Entity\Sales;

use CommonBundle\Entity\Users\Person,
	CudiBundle\Entity\Sales\Session,
	Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\QueueItem")
 * @Table(name="cudi.sales_queue_item")
 */
class QueueItem
{
    /**
	 * @var integer The ID of the queue item
	 *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person of the queue item
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;
    
    /**
     * @var \CudiBundle\Entity\Sales\Session The session of the queue item
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
     * @JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;
    
    /**
     * @var \CudiBundle\Entity\Sales\PayDesk The pay desk of the queue item
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\PayDesk")
     * @JoinColumn(name="pay_desk", referencedColumnName="id")
     */
    private $payDesk;
    
    /**
     * @var integer The number of the queue item
     *
     * @Column(type="smallint", name="queue_number")
     */
    private $queueNumber;
    
    /**
     * @var string The status of the queue item
     *
     * @Column(type="string", length=50)
     */
    private $status;
    
    /**
     * @var \DateTime The time the queue item was created
     *
     * @Column(type="datetime", name="sign_in_time")
     */
    private $signInTime;
    
    /**
     * @var \DateTime The time there were articles sold to the queue item
     *
     * @Column(type="datetime", name="sold_time", nullable=true)
     */
    private $soldTime;
    
    /**
     * @var string The comment of the queue item
     *
     * @Column(type="text", nullable=true)
     */
    private $comment;
    
    /**
     * @var array The possible states of a booking
     */
    private static $POSSIBLE_STATUSES = array(
    	'signed_in', 'collecting', 'collected', 'selling', 'hold', 'canceled', 'sold'
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
    	return in_array($status, self::$POSSIBLE_STATUSES);
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
     * @param string $status
     *
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function setStatus($status)
    {
        if (!self::isValidQueueStatus($status))
        	throw new \InvalidArgumentException('The QueueStatus is not valid.');
        
    	$this->status = $status;
    	
    	switch ($status) {
    	    case 'signed_in':
    	        $this->signInTime = new \DateTime();
    	        break;
    	    case 'sold':
    	        $this->soldTime = new \DateTime();
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
}
