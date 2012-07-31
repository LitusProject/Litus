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
 * This entity represents an appliance thtat is when someone pays by card.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Bank\BankDevice")
 * @Table(name="general.bank_bank_device")
 */
class BankDevice
{
    /**
     * @var int The device's ID
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    
    /**
     * @var string The device's name
     *
     * @Column(type="string")
     */
    private $name;
    
    /**
     * @param string $name The device's name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
