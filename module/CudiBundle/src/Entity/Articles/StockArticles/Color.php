<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Entity\Articles\StockArticles;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\StockArticles\Color")
 * @Table(name="cudi.articles_stockarticles_color")
 */
class Color
{
	/**
	 * @var integer The ID of the color
	 *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
	private $id;
	
	/**
	 * @var string The name of the color
	 *
     * @Column(type="string")
     */
    private $name;
	
	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}
