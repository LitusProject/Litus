<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\Delivery")
 * @Table(name="cudi.stock_delivery")
 */
class Delivery
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="integer")
	 */
	private $price;
	
	/**
	 * @param float $price The price of this delivery
	 */
	public function __construct($price)
	{
		$this->date = new \DateTime();
		$this->price = $this->setPrice($price);
	}
	
	/**
	 * Get the id of this delivery
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set the price of this delivery
	 *
	 * @param float $price The price of this delivery
	 */
	public function setPrice($price)
	{
		$this->price = $price * 100;
		return $this;
	}
	
	/**
	 * Get the price of this delivery
	 *
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	/**
	 * Set the date of this delivery
	 *
	 * @param DateTime $date The date of this delivery
	 */
	public function setDate($date)
	{
		$this->date = $date;
		return $this;
	}
	
	/**
	 * Get the date of this delivery
	 *
	 * @return DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
}
