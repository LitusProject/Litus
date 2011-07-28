<?php

namespace Litus\Entity\Cudi;

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
}
