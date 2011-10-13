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
<<<<<<< HEAD
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Supplier")
	 */
=======
     * @ManyToOne(targetEntity="Litus\Entity\Cudi\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
>>>>>>> 90cc12d5a571317dda2cdabebd0c98003e951f26
	private $supplier;
	
	/**
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="float")
	 */
	private $price;
}
