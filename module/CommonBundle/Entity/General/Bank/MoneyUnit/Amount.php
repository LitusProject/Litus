<?php

namespace CommonBundle\Entity\General\Bank\MoneyUnit;

use CommonBundle\Entity\General\Bank\CashRegister;
use CommonBundle\Entity\General\Bank\MoneyUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\MoneyUnit\Amount")
 * @ORM\Table(name="general_bank_money_units_amounts")
 */
class Amount
{
    /**
     * @var string The amount's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CashRegister The cash register this amount is assigned to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister", inversedBy="moneyUnitAmounts")
     * @ORM\JoinColumn(name="cash_register_id", referencedColumnName="id")
     */
    private $cashRegister;

    /**
     * @var MoneyUnit The unit for which this is the amount
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\MoneyUnit")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var integer The number of units
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @param CashRegister The cash register this amount is assigned to
     * @param MoneyUnit The unit for which this is the amount
     * @param integer                                                   $amount The number of units
     */
    public function __construct(CashRegister $register, MoneyUnit $unit, $amount)
    {
        $this->cashRegister = $register;
        $this->unit = $unit;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\General\Bank\CashRegister
     */
    public function getCashRegister()
    {
        return $this->cashRegister;
    }

    /**
     * @return MoneyUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setAmount($number)
    {
        $this->amount = $number;

        return $this;
    }

    /**
     * Returns the value of the amount.
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->amount * $this->unit->getUnit();
    }
}
