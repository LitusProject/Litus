<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\NumberMoneyUnit")
 * @Table(name="cudi.sales_number_money_unit")
 */
class NumberMoneyUnit
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\CashRegister", inversedBy="numberMoneyUnits")
	 * @JoinColumn(name="cash_register_id", referencedColumnName="id")
	 */
	private $cashRegister;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\MoneyUnit")
	 * @JoinColumn(name="unit_id", referencedColumnName="id")
	 */
	private $unit;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
	
	public function __construct($register, $unit, $number)
	{
		$this->cashRegister = $register;
		$this->unit = $unit;
		$this->number = $number;
	}
	
	public function getUnit()
	{
		return $this->unit;
	}
	
	public function getNumber()
	{
		return $this->number;
	}
	
	public function setNumber($number)
	{
		$this->number = $number;
		return $this;
	}
	
	public function getValue()
	{
		return $this->number*$this->unit->getUnit();
	}
}