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
 
namespace CommonBundle\Entity\Users;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * This entity stores a user's barcode.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Barcode")
 * @Table(name="users.barcodes")
 */
class Barcode
{
    /**
     * @var int The ID of this credential
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \CommonBundle\Entity\Users\Person The person associated with this barcode
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person", inversedBy="barcodes")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The barcode
     *
     * @Column(type="bigint")
     */
    private $barcode;
    
    /**
     * @var \DateTime The time of creation
     *
     * @Column(type="datetime")
     */
    private $time;

    /**
     * Constructs a new barcode
     *
     * @param \CommonBundle\Entity\Users\Person $person
     * @param integer $barcode
     */
    public function __construct(Person $person, $barcode)
    {
        $this->person = $person;
        $this->time = new DateTime();
        
        if (strlen($barcode) == 13)
            $barcode = floor($barcode / 10);
            
        if (strlen($barcode) != 12)
            throw new \InvalidArgumentException('Invalid barcode given: ' . $algorithm);
        
        $this->barcode = $barcode;
    }
    
    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }
}