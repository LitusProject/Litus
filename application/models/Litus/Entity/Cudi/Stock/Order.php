<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\Order")
 * @Table(name="cudi.stock_order")
 */
class Order
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
     * @ManyToOne(targetEntity="Litus\Entity\Cudi\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
	private $supplier;
	
	/**
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="integer")
	 */
	private $price;
	
	/**
	 * @OneToMany(targetEntity="Litus\Entity\Cudi\Stock\OrderItem", mappedBy="order")
	 */
	private $orderItems;
	
	/**
	 * @param Litus\Entity\Cudi\Supplier $supplier The supplier of this order
	 * @param float $price The price of this order
	 */
	public function __construct($supplier, $price)
	{
		$this->setSupplier($supplier);
		$this->date = new \DateTime();
		$this->setPrice($price);
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
	 * Set the supplier of this order
	 *
	 * @param Litus\Entity\Cudi\Supplier $supplier The supplier of this order
	 */
	public function setSupplier($supplier)
	{
		$this->supplier = $supplier;
		return $this;
	}
	
	/**
	 * Get the supplier of this order
	 *
	 * @return Litus\Entity\Cudi\Supplier
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}
	
	/**
	 * Set the price of this order
	 *
	 * @param float $price The price of this order
	 */
	public function setPrice($price)
	{
		$this->price = $price * 100;
		return $this;
	}
	
	/**
	 * Get the price of this order
	 *
	 * @return integer
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	/**
	 * Set the date of this order
	 *
	 * @param DateTime $date The date of this order
	 */
	public function setDate($date)
	{
		$this->date = $date;
		return $this;
	}
	
	/**
	 * Get the date of this order
	 *
	 * @return DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * Get the order items
	 *
	 * @return \Doctrine\Common\Collection\ArrayCollection
	 */
	public function getOrderItems()
	{
		return $this->orderItems;
	}
}
