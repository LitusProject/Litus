<?php

namespace CudiBundle\Entity\Stock;

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
     * @ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
	private $supplier;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $date;
	
	/**
	 * @OneToMany(targetEntity="CudiBundle\Entity\Stock\OrderItem", mappedBy="order")
	 */
	private $orderItems;
	
	/**
	 * @param CudiBundle\Entity\Supplier $supplier The supplier of this order
	 */
	public function __construct($supplier)
	{
		$this->setSupplier($supplier);
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
	 * @param CudiBundle\Entity\Supplier $supplier The supplier of this order
	 */
	public function setSupplier($supplier)
	{
		$this->supplier = $supplier;
		return $this;
	}
	
	/**
	 * Get the supplier of this order
	 *
	 * @return CudiBundle\Entity\Supplier
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}
	
	/**
	 * Get the price of this order
	 *
	 * @return integer
	 */
	public function getPrice()
	{
		$price = 0;
		foreach($this->orderItems as $item)
			$price += $item->getPrice();
		return $price;
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
	
	/**
	 * @return boolean
	 */
	public function isPlaced()
	{
		return null !== $this->date;
	}
}
