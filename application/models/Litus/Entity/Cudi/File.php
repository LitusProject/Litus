<?php

namespace Litus\Entity\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\File")
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
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Articles\StockArticles\Internal")
     * @JoinColumn(name="internal_article", referencedColumnName="id")
	 */
	private $internalArticleId;
}