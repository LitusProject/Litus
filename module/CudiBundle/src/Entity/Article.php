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

use DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Article")
 * @Table(name="cudi.articles")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "external"="CudiBundle\Entity\Articles\External",
 *      "internal"="CudiBundle\Entity\Articles\Internal"}
 * )
 */
abstract class Article
{
    /**
     * @var integer The ID of this article
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of this article
     *
     * @Column(type="string")
     */
    private $title;
    
    /**
     * @var string The authors of the article
     *
     * @Column(type="string")
     */
    private $authors;
    
    /**
     * @var string The publishers of the article
     *
     * @Column(type="string")
     */
    private $publishers;
    
    /**
     * @var integer The year the article was published
     *
     * @Column(name="year_published", type="integer", length=4)
     */
    private $yearPublished;
    
    /**
     * @var \DateTime The time the article was created
     *
     * @Column(type="datetime")
     */
    private $timestamp;
    
    /**
     * @var integer The version number of this article
     * 
     * @Column(name="version_number", type="smallint", nullable=true)
     */
    private $versionNumber;
    
    /**
     * @var integer The ISBN number of this article
     *
     * @Column(type="bigint")
     */
    private $isbn;
    
    /**
     * @var string The url with a link to extra information of this article
     *
     * @Column(type="string", nullable=true)
     */
    private $url;
    
    /**
     * @var boolean The flag whether the article is old or not
     *
     * @Column(name="is_history", type="boolean")
     */
    private $isHistory;
    
    /**
     * @var boolean The flag whether the article is just created by a prof
     *
     * @Column(type="boolean")
     */
    private $isProf;
    
    /**
     * @throws \InvalidArgumentException
     *
     * @param string $title The title of the article
     * @param string $authors The authors of the article
     * @param string $publishers The publishers of the article
     * @param integer $yearPublished The year the article was published
     * @param integer $isbn The isbn of the article
     * @param string|null $url The url of the article
     */
    public function __construct($title, $authors, $publishers, $yearPublished, $isbn, $url = null)
    {
        $this->setTitle($title)
            ->setAuthors($authors)
            ->setPublishers($publishers)
            ->setYearPublished($yearPublished)
            ->setVersionNumber(1)
            ->setISBN($isbn)
            ->setURL($url)
            ->setIsHistory(false)
            ->setIsProf(false);
        $this->timestamp = new DateTime();
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
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @param string $title
	 *
     * @return \CudiBundle\Entity\Article
     */
	public function setTitle($title)
	{
		$title = trim($title);
		
		if (strlen($title) == 0)
            throw new \InvalidArgumentException('The article title is not valid.');

		$this->title = $title;
		return $this;
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
	 * @return \CudiBundle\Entity\Article
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
	 * @return \CudiBundle\Entity\Article
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
	 * @return \CudiBundle\Entity\Article
	 */
	public function setYearPublished($yearPublished)
	{
		$this->yearPublished = $yearPublished;
		return $this;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getTimestamp()
	{
	    return $this->timestamp;
	}
	
	/**
	 * @return integer
	 */
	public function getVersionNumber()
	{
	    return $this->versionNumber;
	}
	
	/**
	 * @param integer $versionNumber
	 *
	 * @return \CudiBundle\Entity\Article
	 */
	public function setVersionNumber($versionNumber)
	{
	    $this->versionNumber = $versionNumber;
	    return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getISBN()
	{
	    return $this->isbn;
	}
	
	/**
	 * @param integer $isbn
	 *
	 * @return \CudiBundle\Entity\Article
	 */
	public function setISBN($isbn)
	{
	    $this->isbn = $isbn;
	    return $this;
	}
	
	/**
	 * @return string
	 */
	public function getURL()
	{
	    return $this->url;
	}
	
	/**
	 * @param string $url
	 *
	 * @return \CudiBundle\Entity\Article
	 */
	public function setURL($url)
	{
	    $this->url = $url;
	    return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isHistory()
	{
	    return $this->isHistory;
	}
	
	/**
	 * @param boolean $isHistory
	 *
	 * @return \CudiBundle\Entity\Article
	 */
	public function setIsHistory($isHistory)
	{
	    $this->isHistory = $isHistory;
	    return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isProf()
	{
	    return $this->isProf;
	}
	
	/**
	 * @param boolean $isProf
	 *
	 * @return \CudiBundle\Entity\Article
	 */
	public function setIsProf($isProf)
	{
	    $this->isProf = $isProf;
	    return $this;
	}
	
	/**
	 * @return \CudiBundle\Entity\Article
	 */
	abstract public function duplicate();
	
	/**
	 * @return boolean
	 */
	abstract public function isExternal();
	
	/**
	 * @return boolean
	 */
	abstract public function isInternal();
}