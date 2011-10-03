<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\StockItem")
 * @Table(name="cudi.stock_stockitem")
 */
class StockItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @Column(type="integer")
	 */
	private $numberInStock;
}
