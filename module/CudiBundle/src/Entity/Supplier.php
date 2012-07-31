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
 
namespace CudiBundle\Entity;

use CommonBundle\Entity\General\Address;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Supplier")
 * @Table(name="cudi.supplier")
 */
class Supplier
{
    /**
     * @var integer The ID of the supplier
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var string The name of the supplier
     *
     * @Column(type="string")
     */
    private $name;
    
    /**
     * @var string The phone number of the supplier
     *
     * @Column(type="string", name="phone_number")
     */
    private $phoneNumber;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the supplier
     *
     * @OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
     * @JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The vat number of the supplier
     *
     * @Column(type="string")
     */
    private $vatNumber;
    
    /**
     * @param string $name
     * @param string $phoneNumber
     * @param \CommonBundle\Entity\General\Address $address
     * @param string $vatNumber
     */
    public function __construct($name, $phoneNumber, Address $address, $vatNumber)
    {
        $this->setName($name)
            ->setPhoneNumber($phoneNumber)
            ->setAddress($address)
            ->setVatNumber($vatNumber);
    }
    
    /**
     * @return integer
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
    
    /**
     * @param string $name
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
    
    /**
     * @param string $phoneNumber
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
    
    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * @param \CommonBundle\Entity\General\Address $address
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }
    
    /**
     * @param string $vatNumber
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }
}
