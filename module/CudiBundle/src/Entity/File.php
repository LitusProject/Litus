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

use CudiBundle\Entity\Articles\StockArticles\Internal as InternalArticle;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\File")
 * @Table(name="cudi.file")
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
	 * @var \CudiBundle\Entity\Articles\StockArticles\Internal The article where the file belongs to
	 * 
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\StockArticles\Internal")
     * @JoinColumn(name="internal_article", referencedColumnName="id")
	 */
	private $internalArticle;
	
	/**
	 * @var boolean The flag whether the file is removed
	 *
	 * @Column(type="boolean")
	 */
	private $removed = false;
	
	/**
	 * @var boolean The flag whether the file is enabled (for in ProfBundle)
	 *
	 * @Column(type="boolean")
	 */
	private $enabled = true;
	
	/**
	 * @param string $path
	 * @param string $name
	 * @param string $description
	 * @param \CudiBundle\Entity\Articles\StockArticles\Internal $internalArticle
	 */
	public function __construct($path, $name, $description, InternalArticle $internalArticle)
	{
		$this->setPath($path);
		$this->setName($name);
		$this->setDescription($description);
		$this->setInternalArticle($internalArticle);
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
	 * @return \CudiBundle\Entity\File
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
	 * @return \CudiBundle\Entity\File
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
	 * @return \CudiBundle\Entity\File
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}
	
	/** 
	 * @return \CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function getInternalArticle()
	{
		return $this->internalArticle;
	}
	
	/** 
	 * @param \CudiBundle\Entity\Articles\StockArticles\Internal $internalArticle
	 *
	 * @return \CudiBundle\Entity\File
	 */
	public function setInternalArticle(InternalArticle $internalArticle)
	{
		$this->internalArticle = $internalArticle;
		return $this;
	}
	
	/**
	 * @param boolean $removed Whether this item is removed or not
	 *
	 * @return \CudiBundle\Entity\File
	 */
	public function setRemoved($removed)
	{
		$this->removed = $removed;
		return $this;
	}
	
	/**
	 * @param boolean
	 * @return \CudiBundle\Entity\File
	 */
	public function setEnabled($enabled = true)
	{
	    $this->enabled = $enabled;
	    return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isEnabled()
	{
	    return $this->enabled;
	}
}