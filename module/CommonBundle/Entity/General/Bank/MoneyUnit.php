<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Entity\General\Bank;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represnts a money unit, e.g. a €1 coin.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Bank\MoneyUnit")
 * @ORM\Table(name="general.bank_money_units")
 */
class MoneyUnit
{
    /**
     * @var int The unit's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int The unit's size
     *
     * @ORM\Column(type="integer")
     */
    private $unit;

    /**
     * @param float $unit The unit's size, multiplied by a 100 before it is stored
     */
    public function __construct($unit)
    {
        $this->unit = $unit * 100;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
