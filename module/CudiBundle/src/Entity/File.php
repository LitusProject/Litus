<?php

namespace Litus\Entity\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\File")
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
	 * @ManyToOne(targetEntity="Litus\Entity\Cudi\Articles\StockArticles\Internal")
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
	 * @return \Litus\Entity\Cudi\File
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
	 * @return \Litus\Entity\Cudi\File
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
	 * @return \Litus\Entity\Cudi\File
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/** 
	 * @return \Litus\Entity\Cudi\Articles\StockArticles\Internal
	 */
	public function getInternalArticle()
	{
		return $this->internalArticle;
	}
	
	/** 
	 * @param \Litus\Entity\Cudi\Articles\StockArticles\Internal $internalArticle
	 *
	 * @return \Litus\Entity\Cudi\File
	 */
	public function setInternalArticle($internalArticle)
	{
		$this->internalArticle = $internalArticle;
	}
}