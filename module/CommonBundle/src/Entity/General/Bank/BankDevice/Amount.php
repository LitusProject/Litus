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
 
namespace CommonBundle\Entity\General\Bank\BankDevice;

use CommonBundle\Entity\General\BankDevice,
	CommonBundle\Entity\General\CashRegister;

/**
 * For a given register, this class has the amount 
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Bank\BankDevice\Amount")
 * @Table(name="general.bank_bank_device_amount")
 */
class Amount
{
	/**
	 * @var string The amount's ID
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CommonBundle\Entity\General\CashRegister The cash register this amount is assigned to
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister", inversedBy="bankDeviceAmounts")
	 * @JoinColumn(name="cash_register_id", referencedColumnName="id")
	 */
	private $cashRegister;
	
	/**
	 * @var \CommonBundle\Entity\General\BankDevice The device that received the payments
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\BankDevice")
	 * @JoinColumn(name="device_id", referencedColumnName="id")
	 */
	private $device;
	
	/**
	 * @var int The amount payed, multiplied by a 100 before it is stored
	 *
	 * @Column(type="integer")
	 */
	private $amount;
	
	/**
	 * @param \CommonBundle\Entity\General\CashRegister The cash register this amount is assigned to
	 * @param \CommonBundle\Entity\General\BankDevice The device that received the payments
	 * @param float $amount The amount payed
	 */
	public function __construct(CashRegister $cashRegister, BankDevice $bankDevice, $amount)
	{
		$this->cashRegister = $cashRegister;
		$this->device = $bankDevice;
		$this->setAmount($amount);
	}
	
	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \CommonBundle\Entity\General\BankDevice
	 */
	public function getDevice()
	{
		return $this->device;
	}
	
	/**
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount / 100;
	}
	
	/**
	 * @param int $amount The amount payed
	 * @return \CommonBundle\Entity\General\Bank\BankDevice\Amount	 
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount * 100;
		
		return $this;
	}
}