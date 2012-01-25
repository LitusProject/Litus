<?php

namespace Litus\Entity\General\Bank;

/**
 * @Entity(repositoryClass="Litus\Repository\General\Bank\BankDeviceAmount")
 * @Table(name="bank_bank_device_amount")
 */
class BankDeviceAmount
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\General\Bank\CashRegister", inversedBy="bankDeviceAmounts")
	 * @JoinColumn(name="cash_register_id", referencedColumnName="id")
	 */
	private $cashRegister;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\General\Bank\BankDevice")
	 * @JoinColumn(name="device_id", referencedColumnName="id")
	 */
	private $device;
	
	/**
	 * @Column(type="integer")
	 */
	private $amount;
	
	public function __construct($register, $device, $amount)
	{
		$this->cashRegister = $register;
		$this->device = $device;
		$this->setAmount($amount);
	}
	
	public function getDevice()
	{
		return $this->device;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function setAmount($amount)
	{
		$this->amount = $amount * 100;
		return $this;
	}
}