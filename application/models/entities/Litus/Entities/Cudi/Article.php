<?php

namespace Litus\Entities\Cudi;

use Litus\Entities\Cudi\Articles\MetaInfo;

use DateTime;

/**
 * @Entity
 * @Table(name="cudi.articles")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({"stub"="Litus\Entities\Cudi\Articles\Stub", "external"="Litus\Entities\Cudi\Articles\StockArticles\External", "internal"="Litus\Entities\Cudi\Articles\StockArticles\Internal"})
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
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Articles\MetaInfo")
     * @JoinColumn(name="metainfo_id", referencedColumnName="id")
	 */
	private $metaInfo;
	
	/**
	 * @Column(type="datetime")
	 */
	private $timestamp;
	
	/**
	 * @param string $title The title of the article
	 * @param Litus\Entities\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
	 * 
	 * @throws InvalidArgumentException If the given meta info object already has an article linked to it.
	 */
	public function __construct($title, $metaInfo)
	{
		if ($metaInfo->getArticle() != null)
			throw new InvalidArgumentException('');
		
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
	 * @return MetaInfo
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
}
