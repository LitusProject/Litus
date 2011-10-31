<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\MoneyUnitAmount")
 * @Table(name="cudi.sales_money_unit_amount")
 */
class MoneyUnitAmount
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\CashRegister", inversedBy="moneyUnitAmounts")
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
	private $amount;
	
	public function __construct($register, $unit, $amount)
	{
		$this->cashRegister = $register;
		$this->unit = $unit;
		$this->amount = $amount;
	}
	
	public function getUnit()
	{
		return $this->unit;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function setAmount($number)
	{
		$this->amount = $number;
		return $this;
	}
	
	public function getValue()
	{
		return $this->amount * $this->unit->getUnit();
	}
}