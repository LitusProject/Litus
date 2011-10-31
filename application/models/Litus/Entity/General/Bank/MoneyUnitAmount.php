<?php

namespace Litus\Entity\General\Bank;

/**
 * @Entity(repositoryClass="Litus\Repository\General\Bank\MoneyUnitAmount")
 * @Table(name="bank_money_unit_amountt")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\General\Bank\CashRegister", inversedBy="moneyUnitAmounts")
	 * @JoinColumn(name="cash_register_id", referencedColumnName="id")
	 */
	private $cashRegister;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\General\Bank\MoneyUnit")
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