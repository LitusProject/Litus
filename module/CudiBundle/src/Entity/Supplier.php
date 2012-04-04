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

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Supplier")
 * @Table(name="cudi.supplier")
 */
class Supplier
{
	/**
	 * @var integer The ID of this supplier
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var string The name of this supplier
	 *
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @var string The phone number of this supplier
	 *
	 * @Column(type="string", name="phone_number")
	 */
	private $phoneNumber;

	/**
	 * @var string The address of this supplier
	 *
	 * @Column(type="string")
	 */
	private $address;

	/**
	 * @var string The vat number of this supplier
	 *
	 * @Column(type="string")
	 */
	private $VAT_number;
	
	/**
	 * @param string $name
	 * @param string $phoneNumber
	 * @param string $address
	 * @param string $VAT
	 */
	public function __construct($name, $phoneNumber, $address, $VAT)
	{
		$this->name = $name;
		$this->phoneNumber = $phoneNumber;
		$this->address = $address;
		$this->VAT_number = $VAT;
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
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
    
    /**
     * @param string
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * @param string
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getVATNumber()
    {
        return $this->VAT_number;
    }
    
    /**
     * @param string
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setVATNumber($VAT)
    {
        $this->VAT_number = $VAT;
        return $this;
    }
}
