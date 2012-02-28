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
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @Column(type="string", name="telephone_number")
	 */
	private $telephoneNumber;

	/**
	 * @Column(type="string")
	 */
	private $address;

	/**
	 * @Column(type="string")
	 */
	private $VAT_number;
	
	/**
	 * @param string $name
	 * @param string $telephoneNumber
	 * @param string $address
	 * @param string $VAT
	 */
	public function __construct($name, $telephoneNumber, $address, $VAT)
	{
		$this->name = $name;
		$this->telephoneNumber = $telephoneNumber;
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
        return $this->telephoneNumber;
    }
    
    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * @return string
     */
    public function getVATNumber()
    {
        return $this->VAT_number;
    }
}
