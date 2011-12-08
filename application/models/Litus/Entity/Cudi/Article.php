<?php

namespace Litus\Entity\Cudi;

use \DateTime;

use \Litus\Entity\Cudi\Articles\MetaInfo;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Article")
 * @Table(name="cudi.articles")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "stub"="Litus\Entity\Cudi\Articles\Stub",
 *      "external"="Litus\Entity\Cudi\Articles\StockArticles\External",
 *      "internal"="Litus\Entity\Cudi\Articles\StockArticles\Internal"}
 * )
 */
abstract class Article
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
    private $title;

    /**
     * @OneToOne(targetEntity="Litus\Entity\Cudi\Articles\MetaInfo")
     * @JoinColumn(name="metainfo", referencedColumnName="id")
     */
    private $metaInfo;

    /**
     * @Column(type="datetime")
     */
    private $timestamp;

	/**
	 * @Column(type="boolean")
	 */
	private $removed = false;
	
	/**
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Stock\StockItem", mappedBy="article")
	 */
	private $stockItem;
	
	/**
	 * @Column(name="version_number", type="smallint", nullable=true)
	 */
	private $versionNumber = 1;

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $title The title of the article
     * @param Litus\Entity\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     */
    public function __construct($title, $metaInfo)
    {
        if ($metaInfo->getArticle() != null)
            throw new \InvalidArgumentException('The meta info is not valid.');

        $this->setTitle($title);
        $this->metaInfo = $metaInfo;
        $metaInfo->setArticle($this);
        $this->timestamp = new DateTime();
    }

    /**
     * @return bigint
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
     * @return \Litus\Entity\Cudi\Article
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
     * @return \Litus\Entity\Cudi\Articles\MetaInfo
     */
    public function getMetaInfo()
    {
        return $this->metaInfo;
    }

    /**
     * @return datetime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
	
	/**
     * @param boolean $removed Whether this item is removed or not
	 *
	 * @return \Litus\Entity\Cudi\Article
     */
	public function setRemoved($removed)
	{
		$this->removed = $removed;
		return $this;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Stock\StockItem
	 */
	public function getStockItem()
	{
		return $this->stockItem;
	}
	
	/**
	 * @param integer $versionNumber The version number of this article
	 *
	 * @return \Litus\Entity\Cudi\Article
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
	 * @return boolean
	 */
	abstract public function isInternal();
	
	/**
	 * @return boolean
	 */
	abstract public function isStock();
}
