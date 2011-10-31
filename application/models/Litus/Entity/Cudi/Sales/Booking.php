<?php

namespace Litus\Entity\Cudi\Sales;

use \Litus\Entity\Cudi\Sales\BookingStatus;
use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\Booking")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
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
	
	public function __construct($person, $article, $status)
	{
		if (!self::isValidBookingStatus($status))
			throw new \InvalidArgumentException('The BookingStatus is not valid.');
			
		if (!isset($article))
			throw new \InvalidArgumentException('The article is not valid.');
			
		if (!isset($person))
			throw new \InvalidArgumentException('The person is not valid.');
			
		if (!$article->isBookable())
			throw new \InvalidArgumentException('The Stock Article cannot be booked.');
		
		$this->person = $person;
		$this->article = $article;
		$this->status = $status;
		$this->bookDate = new \DateTime();
		
		if ($article->canExpire()) {
			$expireTime = Registry::get(DoctrineResource::REGISTRY_KEY)
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
	 * @return \Litus\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Article
	 */
	public function getArticle()
	{
		return $this->article;
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
}
