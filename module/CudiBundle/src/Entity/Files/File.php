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
 
namespace CudiBundle\Entity\Filesize;

use CudiBundle\Entity\Articles\Internal as InternalArticle;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Files\File")
 * @Table(name="cudi.files_file")
 */
class File
{
	/**
	 * @var integer The ID of the file
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var string The path to the file
	 * 
	 * @Column(type="string")
	 */
	private $path;
	
	/**
	 * @var string The name of the file
	 * 
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @var string The description of the file
	 * 
	 * @Column(type="string")
	 */
	private $description;
	
	/**
	 * @var boolean The flag whether the file is just created by a prof
	 *
	 * @Column(type="boolean")
	 */
	private $isProf;
	
	/**
	 * @param string $path The path to the file
	 * @param string $name The name of the file
	 * @param string $description The description of the file
	 */
	public function __construct($path, $name, $description)
	{
		$this->setPath($path)
		    ->setName($name)
		    ->setDescription($description)
		    ->setIsProf(false);
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
	public function getPath()
	{
		return $this->path;
	}
	
	/** 
	 * @param string $path
	 *
	 * @return \CudiBundle\Entity\Files\File
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
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
	 * @return \CudiBundle\Entity\Files\File
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * @param string $description
	 *
	 * @return \CudiBundle\Entity\Files\File
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}
	
	/**
	 * @param boolean $isProf
	 *
	 * @return \CudiBundle\Entity\Files\File
	 */
	public function setIsProf($isProf)
	{
	    $this->isProf = $isProf;
	    return $this;
	}
}