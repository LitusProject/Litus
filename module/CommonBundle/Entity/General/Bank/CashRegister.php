<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Bank;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A class that is used to store the contents of a counted register
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\CashRegister")
 * @ORM\Table(name="general.bank_cash_registers")
 */
class CashRegister
{
    /**
     * @var string This register's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var ArrayCollection The amounts of each money unit
     *
     * @ORM\OneToMany(
     *        targetEntity="CommonBundle\Entity\General\Bank\MoneyUnit\Amount", mappedBy="cashRegister", cascade={"persist", "remove"}
     * )
     */
    private $moneyUnitAmounts;

    /**
     * @var ArrayCollection The amounts of each bank device
     *
     * @ORM\OneToMany(
     *        targetEntity="CommonBundle\Entity\General\Bank\BankDevice\Amount", mappedBy="cashRegister", cascade={"persist", "remove"}
     * )
     */
    private $bankDeviceAmounts;

    public function __construct()
    {
        $this->moneyUnitAmounts = new ArrayCollection();
        $this->bankDeviceAmounts = new ArrayCollection();
    }

    /**
     * @return string
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
     * Get the register's total amount.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        $amount = 0;

        foreach ($this->bankDeviceAmounts as $device) {
            $amount += $device->getAmount();
        }

        foreach ($this->moneyUnitAmounts as $number) {
            $amount += $number->getAmount() * $number->getUnit()->getUnit();
        }

        return (int) $amount;
    }

    /**
     * Get the amount for a unit.
     *
     * @param  MoneyUnit $unit The unit for which we want to get the amount
     * @return int
     */
    public function getAmountForUnit(MoneyUnit $unit)
    {
        foreach ($this->moneyUnitAmounts as $amount) {
            if ($amount->getUnit() == $unit) {
                return $amount->getAmount();
            }
        }

        return 0;
    }

    /**
     * Set the amount for a unit.
     *
     * @param MoneyUnit $unit
     * @param int       $newAmount
     *
     * @return self
     */
    public function setAmountForUnit(MoneyUnit $unit, $newAmount)
    {
        $previous = null;

        foreach ($this->moneyUnitAmounts as $amount) {
            if ($amount->getUnit() == $unit) {
                $previous = $amount;
            }
        }

        if (null === $previous) {
            $this->moneyUnitAmounts[] = new MoneyUnit\Amount($this, $unit, $newAmount);
        } else {
            $previous->setAmount($newAmount);
        }

        return $this;
    }

    /**
     * Get 100 times the amount for a bank device.
     *
     * @param  BankDevice $device The device for which we want to get the amount
     * @return int
     */
    public function getAmountForDevice(BankDevice $device)
    {
        foreach ($this->bankDeviceAmounts as $amount) {
            if ($amount->getDevice() == $device) {
                return $amount->getAmount();
            }
        }

        return 0;
    }

    /**
     * Set the amount for a bank device
     *
     * @param BankDevice $device
     * @param float      $newAmount
     *
     * @return self
     */
    public function setAmountForDevice(BankDevice $device, $newAmount)
    {
        $previous = null;

        foreach ($this->bankDeviceAmounts as $amount) {
            if ($amount->getDevice() == $device) {
                $previous = $amount;
            }
        }

        if (null === $previous) {
            $this->bankDeviceAmounts[] = new BankDevice\Amount($this, $device, $newAmount);
        } else {
            $previous->setAmount($newAmount);
        }

        return $this;
    }
}
