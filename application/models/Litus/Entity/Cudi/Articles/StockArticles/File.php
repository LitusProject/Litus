<?php

namespace Litus\Entity\Cudi\Articles\StockArticles;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\StockArticles\File")
 * @Table(name="cudi.articles_stockarticles_file")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Articles\StockArticles\Internal")
	 * @JoinColumn(name="internal_article_id", referencedColumnName="id")
	 */
	private $internalArticle;
	
	public function __construct($path, $name, $internalArticle) {
		$this->setPath($path)
			->setName($name)
			->setInternalArticle($internalArticle);
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
	 * @return \Litus\Entity\Cudi\Articles\StockArticles\File
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
	 * @return \Litus\Entity\Cudi\Articles\StockArticles\File
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
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
	 * @return \Litus\Entity\Cudi\Articles\StockArticles\File
	 */
	public function setInternalArticle($internalArticle)
	{
		$this->internalArticle = $internalArticle;
		return $this;
	}
}
