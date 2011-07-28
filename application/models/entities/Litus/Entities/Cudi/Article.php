<?php

namespace Litus\Entities\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Article")
 * @Table(name="cudi.articles")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "stub"="Litus\Entities\Cudi\Articles\Stub",
 *      "external"="Litus\Entities\Cudi\Articles\StockArticles\External",
 *      "internal"="Litus\Entities\Cudi\Articles\StockArticles\Internal"}
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
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Articles\MetaInfo")
     * @JoinColumn(name="metainfo_id", referencedColumnName="id")
	 */
	private $metaInfo;
	
	/**
	 * @Column(type="datetime")
	 */
	private $timestamp;
}
