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

namespace CommonBundle\Entity\General\Bank\BankDevice;

use CommonBundle\Entity\General\Bank\BankDevice;
use CommonBundle\Entity\General\Bank\CashRegister;
use Doctrine\ORM\Mapping as ORM;

/**
 * For a given register, this class has the amount
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\BankDevice\Amount")
 * @ORM\Table(name="general_bank_bank_devices_amounts")
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
     * @var integer The amount payed, multiplied by a 100 before it is stored
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister The cash register this amount is assigned to
     * @param \CommonBundle\Entity\General\Bank\BankDevice The device that received the payments
     * @param float                                                                                       $amount The amount payed
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
     * @return \CommonBundle\Entity\General\Bank\CashRegister
     */
    public function getCashRegister()
    {
        return $this->cashRegister;
    }

    /**
     * @return \CommonBundle\Entity\General\Bank\BankDevice
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param  integer $amount The amount payed
     * @return Amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount * 100;

        return $this;
    }
}
