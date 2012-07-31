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
 
namespace CommonBundle\Entity\General\Bank;

/**
 * This class represnts a money unit, e.g. a €1 coin.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Bank\MoneyUnit")
 * @Table(name="general.bank_money_unit")
 */
class MoneyUnit
{
    /**
     * @var int The unit's ID
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    
    /**
     * @var int The unit's size
     *
     * @Column(type="integer")
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
