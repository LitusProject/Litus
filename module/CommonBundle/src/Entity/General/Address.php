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
 
namespace CommonBundle\Entity\General;

/**
 * This class represents a address entry that is saved in the database
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Address")
 * @Table(name="general.address")
 */
class Address 
{
    /**
     * @var integer The ID of the address 
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The street
     *
     * @Column(type="string")
     */
    private $street;

    /**
     * @var string The house number
     *
     * @Column(type="string")
     */
    private $number;
    
    /**
     * @var string The postal
     *
     * @Column(type="string")
     */
    private $postal;
    
    /**
     * @var string The city
     *
     * @Column(type="string")
     */
    private $city;
    
    /**
     * @var string The country
     *
     * @Column(type="string")
     */
    private $country;
	
	/**
	 * @param string $street
	 * @param string $number
	 * @param string $postal
	 * @param string $city
	 * @param string $country
	 */
	public function __construct($street, $number, $postal, $city, $country)
	{
	    $this->street = $street;
	    $this->number = $number;
	    $this->postal = $postal;
	    $this->city = $city;
	    $this->country = $country;
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
    public function getStreet()
    {
        return $this->street;
    }
    
    /**
     * @param string $street
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }
	
	/**
	 * @return string
	 */
	public function getNumber()
	{
	    return $this->number;
	}
	
	/**
	 * @param string $number
	 *
	 * @return \CommonBundle\Entity\General\Address
	 */
	public function setNumber($number)
	{
	    $this->number = $number;
	    return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPostal()
	{
	    return $this->postal;
	}
	
	/**
	 * @param string $postal
	 *
	 * @return \CommonBundle\Entity\General\Address
	 */
	public function setPostal($postal)
	{
	    $this->postal = $postal;
	    return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCity()
	{
	    return $this->city;
	}
	
	/**
	 * @param string $city
	 *
	 * @return \CommonBundle\Entity\General\Address
	 */
	public function setCity($city)
	{
	    $this->city = $city;
	    return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCountry()
	{
	    return $this->country;
	}
	
	/**
	 * @param string $number
	 *
	 * @return \CommonBundle\Entity\General\Address
	 */
	public function setCountry($country)
	{
	    $this->country = $country;
	    return $this;
	}
}
