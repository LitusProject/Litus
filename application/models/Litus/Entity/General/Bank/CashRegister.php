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
     * @Column(type="integer", nullable=true)
     */
    private $amountBank1;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $amountBank2;
	
    public function __construct($bank1, $bank2)
    {
        $this->setAmountBank1($bank1);
        $this->setAmountBank2($bank2);
    }
	
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
     * @param integer $amountBank1
     */
    public function setAmountBank1($amountBank1)
    {
        $this->amountBank1 = $amountBank1 * 100;
		return $this;
    }

    /**
     * Get amountBank1
     *
     * @return integer 
     */
    public function getAmountBank1()
    {
        return $this->amountBank1;
    }

    /**
     * Set amountBank2
     *
     * @param integer $amountBank2
     */
    public function setAmountBank2($amountBank2)
    {
        $this->amountBank2 = $amountBank2 * 100;
		return $this;
    }

    /**
     * Get amountBank2
     *
     * @return integer 
     */
    public function getAmountBank2()
    {
        return $this->amountBank2;
    }

	/**
	 * Get the total amoun
	 *
	 * @return integer
	 */
	public function getTotalAmount()
    {
        $amount = $this->amountBank1 + $this->amountBank2;
		
		foreach($this->moneyUnitAmounts as $number)
			$amount += $number->getAmount() * $number->getUnit()->getUnit();
		
		return $amount;
    }

	/**
	 * Get number of a unit
	 *
	 * @return \MoneyUnitAmount\Entity\Cudi\Sales\NumberMoneyUnit
	 */
	public function getNumberForUnit($unit)
	{
		foreach($this->moneyUnitAmounts as $number) {
			if ($number->getUnit() == $unit)
				return $number;
		}
	}
}
