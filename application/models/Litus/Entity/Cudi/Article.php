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
     * @JoinColumn(name="metainfo_id", referencedColumnName="id")
     */
    private $metaInfo;

    /**
     * @Column(type="datetime")
     */
    private $timestamp;

    /**
     * @throws \InvalidArgumentException If the given meta info object already has an article linked to it
     *
     * @param string $title The title of the article
     * @param Litus\Entities\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
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
