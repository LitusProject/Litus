<?php

namespace CudiBundle\Entity\Articles;

use CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\MetaInfo")
 * @Table(name="cudi.articles_metainfo")
 */
class MetaInfo
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @Column(type="string")
	 */
	private $authors;
	
	/**
	 * @Column(type="string")
	 */
	private $publishers;
	
	/**
	 * @Column(name="year_published", type="integer", length=4)
	 */
	private $yearPublished;
	
	public function __construct($authors, $publishers, $yearPublished) {
		$this->authors = $authors;
		$this->publishers = $publishers;
		$this->yearPublished = $yearPublished;
	}
	
	/**
	 * @return bigint
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @param Article $article The article to link to this metainfo object.
	 */
	public function setArticle($article)
	{
		$this->article = $article;
	}
	
	/**
	 * @return string
	 */
	public function getAuthors()
	{
		return $this->authors;
	}
	
	/**
     * @param string $authors
	 *
     * @return \CudiBundle\Entity\Articles\MetaInfo
     */
	public function setAuthors($authors)
	{
		$this->authors = $authors;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPublishers()
	{
		return $this->publishers;
	}
	
	/**
     * @param string $publishers
	 *
     * @return \CudiBundle\Entity\Articles\MetaInfo
     */
	public function setPublishers($publishers)
	{
		$this->publishers = $publishers;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getYearPublished()
	{
		return $this->yearPublished;
	}
	
	/**
     * @param string $yearPublished
	 *
     * @return \CudiBundle\Entity\Articles\MetaInfo
     */
	public function setYearPublished($yearPublished)
	{
		$this->yearPublished = $yearPublished;
		return $this;
	}
}