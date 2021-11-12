<?php

namespace CommonBundle\Entity\General\Bank;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represnts a money unit, e.g. a €1 coin.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\MoneyUnit")
 * @ORM\Table(name="general_bank_money_units")
 */
class MoneyUnit
{
    /**
     * @var integer The unit's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer The unit's size
     *
     * @ORM\Column(type="integer")
     */
    private $unit;

    /**
     * @param float $unit The unit's size, multiplied by a 100 before it is stored
     */
    public function __construct($unit)
    {
        $this->unit = (int) ($unit * 100);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
