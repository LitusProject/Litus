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
	 * @static
	 * @var array All the allowed country values
	 */
	public static $countries = array(
		'Americas' => array(
			'CA' => 'Canada',
			'US' => 'United States',
		),
		'Europe' => array(
			'AL' => 'Albania',
			'AD' => 'Andorra',
			'AT' => 'Austria',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BA' => 'Bosnia and Herzegovina',
			'BG' => 'Bulgaria',
			'HR' => 'Croatia',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DD' => 'East Germany',
			'EE' => 'Estonia',
			'FO' => 'Faroe Islands',
			'FI' => 'Finland',
			'FR' => 'France',
			'DE' => 'Germany',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GG' => 'Guernsey',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IT' => 'Italy',
			'JE' => 'Jersey',
			'LV' => 'Latvia',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MK' => 'Macedonia',
			'MT' => 'Malta',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'ME' => 'Montenegro',
			'NL' => 'Netherlands',
			'NO' => 'Norway',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'SM' => 'San Marino',
			'RS' => 'Serbia',
			'CS' => 'Serbia and Montenegro',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'ES' => 'Spain',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'UA' => 'Ukraine',
			'GB' => 'United Kingdom',
			'VA' => 'Vatican City',
		),
	);

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
	    $this->setStreet($street)
	        ->setNumber($number)
	        ->setPostal($postal)
	        ->setCity($city)
	        ->setCountry($country);
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
	public function getCountryCode()
	{
	    return $this->country;
	}
	
	/**
	 * @return string
	 */
	public function getCountry()
	{
	    foreach(self::$countries as $continent) {
	        if (array_key_exists($this->country, $continent))
	            return $continent[$this->country];
	    }
	    return '';
	}
	
	/**
	 * @param string $number
	 *
	 * @return \CommonBundle\Entity\General\Address
	 */
	public function setCountry($country)
	{
		if (self::isValidCountry($country))
			$this->country = $country;
	    
	    return $this;
	}
	
	/**
	 * Checks whether the given status is valid.
	 *
	 * @param $status string A status
	 * @return bool
	 */
	public static function isValidCountry($country)
	{
	    foreach(self::$countries as $continent) {
    	    if (array_key_exists($country, $continent))
    	        return true;
    	}
    	return false;
	}
}
