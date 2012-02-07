<?php

namespace CudiBundle\Entity;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\File")
 * @Table(name="cudi.file")
 */
class File
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
	private $path;
	
	/**
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @Column(type="string")
	 */
	private $description;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\StockArticles\Internal")
     * @JoinColumn(name="internal_article", referencedColumnName="id")
	 */
	private $internalArticle;
	
	public function __construct($path, $name, $description, $internalArticle)
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
	 * @return CudiBundle\Entity\File
	 */
	public function setPath($path)
	{
		$this->path = $path;
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
	 * @return CudiBundle\Entity\File
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * @return CudiBundle\Entity\CudiBundle\File
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/** 
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function getInternalArticle()
	{
		return $this->internalArticle;
	}
	
	/** 
	 * @param CudiBundle\Entity\Articles\StockArticles\Internal $internalArticle
	 *
	 * @return CudiBundle\Entity\File
	 */
	public function setInternalArticle($internalArticle)
	{
		$this->internalArticle = $internalArticle;
	}
}