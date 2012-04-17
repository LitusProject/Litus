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
 
namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\Delta")
 * @Table(name="cudi.stock_delta")
 */
class Delta
{
	/**
	 * @var integer The ID of the delta
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person who added this delta
	 *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
	private $person;
	
	/**
	 * @var integer The delta
	 *
	 * @Column(type="integer")
	 */
	private $delta;
	
	/**
	 * @var \DateTime The time of the delta
	 *
	 * @Column(type="datetime", nullable=true)
	 */
	private $date;
	
	/**
	 * @var \CudiBundle\Entity\Stock\StockItem The stock item
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Stock\StockItem")
	 * @JoinColumn(name="stock_item", referencedColumnName="id")
	 */
	private $stockItem;
	
	/**
	 * @var string The comment
	 *
	 * @Column(type="text")
	 */
	private $comment;
	
	/**
	 * @param \CommonBundle\Entity\Users\Person $person The person who added this delta
	 * @param \CudiBundle\Entity\Stock\StockItem $stockItem The stock item
	 * @param integer $delta The delta
	 * @param string $comment The comment
	 */
	public function __construct(Person $person, StockItem $stockItem, $delta, $comment)
	{
		$this->person = $person;
		$this->date = new DateTime();
		$this->stockItem = $stockItem;
		$this->delta = $delta;
		$this->comment = $comment;
	}
	
	/**
	 * Get the id of this delta
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Get the delta
	 *
	 * @return integer
	 */
	public function getDelta()
	{
		return $this->delta;
	}
	
	/**
	 * Get the person of this delta
	 *
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * Get the date of this order
	 *
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * Get the stock item
	 *
	 * @return \CudiBundle\Entity\Stock\StockItem
	 */
	public function getStockItem()
	{
		return $this->stockItem;
	}
	
	/**
	 * Get the comment
	 *
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}
	
	/**
	 * @return string
	 */
	public function getSummary($length = 50)
	{
	    return substr($this->comment, 0, $length) . (strlen($this->comment) > $length ? '...' : '');
	}
}
