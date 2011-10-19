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
     * @throws \InvalidArgumentException
     *
     * @param string $title The title of the article
     * @param Litus\Entity\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     */
    public function __construct($title, $metaInfo)
    {
        if ($metaInfo->getArticle() != null)
            throw new \InvalidArgumentException('');

        $this->title = $title;
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
}
