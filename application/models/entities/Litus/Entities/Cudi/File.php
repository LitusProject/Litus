<?php

namespace Litus\Entities\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\FileRepository")
 * @Table(name="cudi.file")
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
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Articles\StockArticles\Internal")
     * @JoinColumn(name="internal_article_id", referencedColumnName="id")
	 */
	private $internalArticleId;
}