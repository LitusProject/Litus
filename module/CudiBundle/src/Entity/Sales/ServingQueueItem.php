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
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\ServingQueueItem")
 * @Table(name="cudi.sales_serving_queue_item")
 */
class ServingQueueItem
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\ServingQueueStatus")
     * @JoinColumn(name="status", referencedColumnName="id")
     */
    private $status;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\PayDesk")
     * @JoinColumn(name="pay_desk", referencedColumnName="id")
     */
    private $payDesk;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
     * @JoinColumn(name="sale_session", referencedColumnName="id")
     */
    private $session;
	
    /**
     * @Column(type="smallint")
     */
    private $queueNumber;

	/**
	 * @param Doctrine\ORM\EntityManager $entityManager
	 * @param CommonBundle\Entity\Users\Person $person
	 * @param CudiBundle\Entity\Sales\Session $session
	 */
    public function __construct(EntityManager $entityManager, Person $person, Session $session)
    {
    	$this->person = $person;
    	$this->session = $session;
    	
    	$this->status = $entityManager
    		->getRepository('CudiBundle\Entity\Sales\ServingQueueStatus')
    		->findOneByName('signed_in');
    		
    	$this->queueNumber = $entityManager
    		->getRepository('CudiBundle\Entity\Sales\ServingQueueItem')
    		->getQueueNumber($session);
    }
	
	/**
	 * @return integer
	 */
    public function getId()
    {
		return $this->id;
    }

	/**
	 * @return CommonBundle\Entity\Users\Person
	 */
    public function getPerson()
    {
        return $this->person;
    }
	
	/**
	 * @return CudiBundle\Entity\Sales\ServingQueueStatus
	 */
    public function getStatus()
    {
        return $this->status;
    }
	
	/**
	 * @return CudiBundle\Entity\Sales\PayDesk
	 */
    public function getPayDesk()
    {
        return $this->payDesk;
    }
	
	/**
	 * @param CudiBundle\Entity\Sales\PayDesk
	 *
	 * @return CudiBundle\Entity\Sales\ServingQueueItem
	 */
    public function setPayDesk($payDesk)
    {
        $this->payDesk = $payDesk;
        return $this;
    }
	
	/**
	 * @return CudiBundle\Entity\Sales\Session
	 */
    public function getSession()
    {
        return $this->session;
    }
	
	/**
	 * @return integer
	 */
    public function getQueueNumber() 
   	{
        return $this->queueNumber;
    }
}
