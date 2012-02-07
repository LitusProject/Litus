<?php

namespace CudiBundle\Entity\Articles\StockArticles;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\StockArticles\Color")
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
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
}
