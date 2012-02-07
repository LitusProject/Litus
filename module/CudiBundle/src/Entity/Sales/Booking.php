<?php

namespace CudiBundle\Entity\Sales;

use CudiBundle\Entity\Sales\BookingStatus,

	Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Booking")
 * @Table(name="cudi.sales_booking")
 */
class Booking
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @Column(type="smallint")
	 */
	private $number;
	
	/**
	 * @Column(type="string", length=50)
	 */
	private $status;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $expirationDate;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $assignmentDate;
	
	/**
	 * @Column(type="datetime")
	 */
	private $bookDate;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $saleDate;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $cancelationDate;
	
	private static $POSSIBLE_STATUSES = array(
		'booked', 'assigned', 'sold', 'expired'
	);
	
	public function __construct($entityManager, $person, $article, $status, $number = 1)
	{
		if (!isset($article))
			throw new \InvalidArgumentException('The article is not valid.');
			
		if (!isset($person))
			throw new \InvalidArgumentException('The person is not valid.');
			
		if (!$article->isBookable())
			throw new \InvalidArgumentException('The Stock Article cannot be booked.');
		
		$this->person = $person;
		$this->article = $article;
		$this->number = $number;
		$this->setStatus($status);
		$this->bookDate = new \DateTime();
		
		if ($article->canExpire()) {
			$expireTime = $entityManager
	            ->getRepository('Litus\Entity\General\Config')
	            ->getConfigValue('cudi.reservation_expire_time');
	
			$now = new \DateTime();
			$this->expirationDate = $now->add(new \DateInterval($expireTime));
			
		}
	}
	
	/**
	 * @return boolean
	 */
	public static function isValidBookingStatus($status)
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
	 * @return \Litus\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * @return CudiBundle\Entity\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @param CudiBundle\Entity\Article $article The new article of this booking
	 * 
	 * @return CudiBundle\Entity\Sales\Booking
	 */
	public function setArticle($article)
	{
		$this->article = $article;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getBookDate()
	{
		return $this->bookDate;
	}
	
	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getExpirationDate()
	{
		return $this->expirationDate;
	}
	
	/**
	 * @param string $status The new status of this booking.
	 */
	public function setStatus($status)
	{
		if (!self::isValidBookingStatus($status))
			throw new \InvalidArgumentException('The BookingStatus is not valid.');
		
		if ($status == 'assigned')
			$this->assignmentDate = new \DateTime();
		else
			$this->assignmentDate = null;
		
		$this->status = $status;
	}
	
	/**
	 * @return boolean
	 */
	public function isExpired()
	{
		return $this->expirationDate < new \DateTime();
	}
}
