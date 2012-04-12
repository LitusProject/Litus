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
 * This class represents a configuration entry that is saved in the database
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Config")
 * @Table(name="general.config")
 */
class Config 
{
	/**
	 * @static
	 * @var string The separator used to denote the bundles
	 */
	public static $separator = '.';

    /**
     * @var string The entry's key
     *
     * @Id
     * @Column(type="string")
     */
    private $key;

    /**
     * @var string The entry's value
     *
     * @Column(type="text")
     */
    private $value;

    /**
     * @var string A description for this configuration entry
     *
     * @Column(type="string", nullable=true)
     */
    private $description;

	/**
	 * @param string $key The entry's key
	 * @param string $value The entry's value
	 * @throws \InvalidArgumentException Key must be a string
	 */
    public function __construct($key, $value)
    {
        if(!is_string($key))
            throw new \InvalidArgumentException('Key must be a string');
            
        $this->key = $key;
        $this->setValue($value);
    }
	
	/**
	 * @return string
	 */
    public function getKey()
    {
        return $this->key;
    }
	
	/**
	 * @return string
	 */
    public function getValue()
    {
        return $this->value;
    }
	
	/**
	 * @param string $value The entry's value
	 * @return \CommonBundle\Entity\Public\Config
	 * @throws \InvalidArgumentException Value must be a string
	 */
    public function setValue($value)
    {
        if(!is_string($value))
            throw new \InvalidArgumentException('Value must be a string');
            
        $this->value = $value;
        
        return $this;
    }

	/**
	 * @return string
	 */
    public function getDescription()
    {
        return $this->description;
    }
	
	/**
	* @param string $description A description for this configuration entry
	* @return \CommonBundle\Entity\Public\Config
	* @throws \InvalidArgumentException Description must be a string or null
	 */
    public function setDescription($description = null)
    {
        if(($description !== null) && !is_string($description))
            throw new \InvalidArgumentException('Description must be a string or null');
            
        $this->description = $description;
    }
}
