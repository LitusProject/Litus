<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Bank\BankDevice;

use CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\CashRegister,
    Doctrine\ORM\Mapping as ORM;

/**
 * For a given register, this class has the amount
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\BankDevice\Amount")
 * @ORM\Table(name="general.bank_bank_devices_amounts")
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
     * @var \CommonBundle\Entity\General\Bank\CashRegister The cash register this amount is assigned to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister", inversedBy="bankDeviceAmounts")
     * @ORM\JoinColumn(name="cash_register_id", referencedColumnName="id")
     */
    private $cashRegister;

    /**
     * @var \CommonBundle\Entity\General\Bank\BankDevice The device that received the payments
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Bank\BankDevice")
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id")
     */
    private $device;

    /**
     * @var int The amount payed, multiplied by a 100 before it is stored
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister The cash register this amount is assigned to
     * @param \CommonBundle\Entity\General\Bank\BankDevice The device that received the payments
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
     * @return \CommonBundle\Entity\General\Bank\BankDevice
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
        return $this->amount;
    }

    /**
     * @param int $amount The amount payed
     * @return \CommonBundle\Entity\General\Bank\BankDevice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount * 100;

        return $this;
    }
}
