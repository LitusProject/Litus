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
 
namespace CommonBundle\Entity\General\Bank\MoneyUnit;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Entity\General\Bank\MoneyUnit;

/**
 * @Entity(repositoryClass="CommonBundle\Repository\General\Bank\MoneyUnit\Amount")
 * @Table(name="general.bank_money_unit_amount")
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
     * @var \CommonBundle\Entity\General\Bank\CashRegister The cash register this amount is assigned to
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister", inversedBy="moneyUnitAmounts")
     * @JoinColumn(name="cash_register_id", referencedColumnName="id")
     */
    private $cashRegister;
    
    /**
     * @var CommonBundle\Entity\General\Bank\MoneyUnit The unit for which this is the amount
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\MoneyUnit")
     * @JoinColumn(name="unit_id", referencedColumnName="id")
     */
    private $unit;
    
    /**
     * @var int The number of units
     *
     * @Column(type="integer")
     */
    private $amount;
    
    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister The cash register this amount is assigned to
     * @param \CommonBundle\Entity\General\Bank\MoneyUnit The unit for which this is the amount
     * @param int $amount The number of units
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
     * @return \CommonBundle\Entity\General\Bank\MoneyUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }
    
    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * @param int $amount The number of units
     * @return \CommonBundle\Entity\General\Bank\BankDevice\Amount     
     */
    public function setAmount($number)
    {
        $this->amount = $number;
        
        return $this;
    }
    
    /**
     * Returns the value of the amount.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->amount * $this->unit->getUnit();
    }
}
