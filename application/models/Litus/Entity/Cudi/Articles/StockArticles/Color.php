<?php

namespace Litus\Entity\Cudi\Articles\StockArticles;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\StockArticles\Color")
 * @Table(name="cudi.articles_stockarticles_color")
 */
class Color
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
    private $name;
	
	public function __construct($name) {
		$this->name = $name;
	}
}
