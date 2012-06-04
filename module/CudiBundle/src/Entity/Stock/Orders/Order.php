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
 
namespace CudiBundle\Entity\Stock\Orders;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Supplier,
	DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\Orders\Order")
 * @Table(name="cudi.stock_order", indexes={@index(name="stock_order_time", columns={"date_created"})})
 */
class Order
{
	/**
	 * @var integer The ID of the order
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Supplier The supplier of the order
	 *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
	private $supplier;
	
	/**
	 * @var \DateTime The time the order was created
	 *
	 * @Column(name="date_created", type="datetime")
	 */
	private $dateCreated;
	
	/**
	 * @var \DateTime The time the order was ordered
	 *
	 * @Column(name="date_ordered", type="datetime", nullable=true)
	 */
	private $dateOrdered;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection The items ordered
	 *
	 * @OneToMany(targetEntity="CudiBundle\Entity\Stock\Orders\Item", mappedBy="order")
	 */
	private $items;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person who ordered the order
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="person", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @param \CudiBundle\Entity\Supplier $supplier The supplier of this order
	 */
	public function __construct(Supplier $supplier, Person $person)
	{
		$this->supplier = $supplier;
		$this->person = $person;
		$this->dateCreated = new DateTime();
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
	 * @return \CudiBundle\Entity\Supplier
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
		foreach($this->items as $item)
			$price += $item->getPrice();
		return $price;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDateCreated()
	{
		return $this->dateCreated;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDateOrdered()
	{
		return $this->dateOrdered;
	}
	
	/**
	 * @return \Doctrine\Common\Collection\ArrayCollection
	 */
	public function getItems()
	{
		return $this->items;
	}
	
	/**
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * @param \CommonBundle\Entity\Users\Person $person
	 * 
	 * @return \CudiBundle\Entity\Stock\Orders\Order
	 */
	public function setPerson(Person $person)
	{
		$this->person = $person;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isOrdered()
	{
		return null !== $this->dateOrdered;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\Orders\Order
	 */
	public function order()
	{
		$this->dateOrdered = new DateTime();
		return $this;
	}
}
