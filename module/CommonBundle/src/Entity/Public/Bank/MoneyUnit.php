<?php

namespace Litus\Entity\General\Bank;

/**
 * @Entity(repositoryClass="Litus\Repository\General\Bank\MoneyUnit")
 * @Table(name="bank_money_unit")
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