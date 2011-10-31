<?php

namespace Litus\Entity\General\Bank;

/**
 * @Entity(repositoryClass="Litus\Repository\General\Bank\CashRegister")
 * @Table(name="bank_cash_register")
 */
class CashRegister
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The amounts of each money unit
     *
	 * @OneToMany(targetEntity="\Litus\Entity\General\Bank\MoneyUnitAmount", mappedBy="cashRegister", cascade={"remove"})
	 */
	private $moneyUnitAmounts;
	
	/**
     * @var \Doctrine\Common\Collections\ArrayCollection The amounts of each bank device
     *
	 * @OneToMany(targetEntity="\Litus\Entity\General\Bank\BankDeviceAmount", mappedBy="cashRegister", cascade={"remove"})
	 */
	private $bankDeviceAmounts;
	
	/**
	 * @return integer
	 */
    public function getId()
	{
        return $this->id;
    }

	/**
	 * @return array
	 */
	public function getMoneyUnitAmounts()
	{
		return $this->moneyUnitAmounts->toArray();
	}

  	/**
	 * @return array
	 */
	public function getBankDeviceAmounts()
	{
		return $this->bankDeviceAmounts->toArray();
	}

	/**
	 * Get the total amount
	 *
	 * @return integer
	 */
	public function getTotalAmount()
    {
        $amount = 0;

		foreach($this->bankDeviceAmounts as $device)
			$amount += $device->getAmount();
		
		foreach($this->moneyUnitAmounts as $number)
			$amount += $number->getAmount() * $number->getUnit()->getUnit();
		
		return $amount;
    }

	/**
	 * Get amount of a unit
	 *
	 * @return \MoneyUnitAmount\Entity\General\Bank\NumberMoneyUnit
	 */
	public function getAmountForUnit($unit)
	{
		foreach($this->moneyUnitAmounts as $amount) {
			if ($amount->getUnit() == $unit)
				return $amount;
		}
	}
	
	/**
	 * Get amount of a bank device
	 *
	 * @return \MoneyUnitAmount\Entity\General\Bank\BankDeviceAmount
	 */
	public function getAmountForDevice($device)
	{
		foreach($this->bankDeviceAmounts as $amount) {
			if ($amount->getDevice() == $device)
				return $amount;
		}
	}
}
