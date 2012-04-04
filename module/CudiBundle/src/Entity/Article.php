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

use DateTime,
	CudiBundle\Entity\Articles\MetaInfo;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Article")
 * @Table(name="cudi.articles")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "stub"="CudiBundle\Entity\Articles\Stub",
 *      "external"="CudiBundle\Entity\Articles\StockArticles\External",
 *      "internal"="CudiBundle\Entity\Articles\StockArticles\Internal"}
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
     * @var \CudiBundle\Entity\Articles\MetaInfo The metainfo of this article
     *
     * @OneToOne(targetEntity="CudiBundle\Entity\Articles\MetaInfo")
     * @JoinColumn(name="metainfo", referencedColumnName="id")
     */
    private $metaInfo;

    /**
     * @var \DateTime The time the article was created
     *
     * @Column(type="datetime")
     */
    private $timestamp;

	/**
	 * @var boolean The flag whether the article is removed
	 *
	 * @Column(type="boolean")
	 */
	private $removed = false;
	
	/**
	 * @var \CudiBundle\Entity\Stock\StockItem The reference to a stock item
	 *
	 * @OneToOne(targetEntity="CudiBundle\Entity\Stock\StockItem", mappedBy="article")
	 */
	private $stockItem;
	
	/**
	 * @var integer The version number of this article
	 * 
	 * @Column(name="version_number", type="smallint", nullable=true)
	 */
	private $versionNumber = 1;
	
	/**
	 * @var boolean The flag whether the article is enabled (for in ProfBundle)
	 *
	 * @Column(type="boolean")
	 */
	private $enabled = true;

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $title The title of the article
     * @param \CudiBundle\Entity\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     */
    public function __construct($title, MetaInfo $metaInfo)
    {
        if ($metaInfo->getArticle() != null)
            throw new \InvalidArgumentException('The meta info is not valid.');

        $this->setTitle($title);
        $this->metaInfo = $metaInfo;
        $metaInfo->setArticle($this);
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
     * @return \CudiBundle\Entity\Articles\MetaInfo
     */
    public function getMetaInfo()
    {
        return $this->metaInfo;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
	
	/**
     * @param boolean $removed Whether this item is removed or not
	 *
	 * @return \CudiBundle\Entity\Article
     */
	public function setRemoved($removed)
	{
		$this->removed = $removed;
		return $this;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\StockItem
	 */
	public function getStockItem()
	{
		return $this->stockItem;
	}
	
	/**
	 * @param integer $versionNumber The version number of this article
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
	public function getVersionNumber()
	{
		return $this->versionNumber;
	}
	
	/**
	 * @param boolean
	 * @return \CudiBundle\Entity\Article
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
	
	/**
	 * @return boolean
	 */
	abstract public function isInternal();
	
	/**
	 * @return boolean
	 */
	abstract public function isStock();
}
