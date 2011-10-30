<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\MoneyUnit")
 * @Table(name="cudi.sales_money_unit")
 */
class MoneyUnit
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="integer")
	 */
	private $id;
	
	/**
	 * @Column(type="integer")
	 */
	private $unit;
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getUnit()
	{
		return $this->unit;
	}
}