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
 
namespace CudiBundle\Entity\Articles\Discount;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\Discount\Discount")
 * @Table(name="cudi.articles_discount")
 */
class Discount
{
	/**
	 * @var integer The ID of the discount
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of this discount
	 * 
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var integer The value of the discount
	 *
	 * @Column(type="integer")
	 */
	private $value;
	
	/**
	 * @var string The type of the discount
	 *
	 * @Column(type="string")
	 */
	private $method;
	
	/**
	 * @var \CudiBundle\Entity\Articles\Discount\Type The type of this discount
	 * 
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\Discount\Type")
	 * @JoinColumn(name="type", referencedColumnName="id")
	 */
	private $type;
	
	/**
	 * @var array The possible methods of a discount
	 */
	private static $POSSIBLE_METHODS = array(
		'percentage', 'fixed'
	);
	
	/**
	 * @param \CudiBundle\Entity\Article $article
	 * @param integer $value
	 * @param string $method
	 */
	public function __construct(Article $article, $value, $method, Type $type) {
		$this->article = $article;
		$this->value = $value;
		$this->type = $type;
		
		if (!self::isValidDiscountMethod($method))
			throw new \InvalidArgumentException('The discount method is not valid.');
		$this->method = $method;
		
		if ($method !== 'percentage')
		    $this->value = $this->value * 100;
	}
	
	/**
	 * @return boolean
	 */
	public static function isValidDiscountMethod($method)
	{
		return in_array($method, self::$POSSIBLE_METHODS);
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \CudiBundle\Entity\Article
	 */
	public function getArticle()
	{
	    return $this->article;
	}
	
	/**
	 * @return integer
	 */
	public function getValue()
	{
	    return $this->value;
	}
	
	/**
	 * @return string
	 */
	public function getMethod()
	{
	    return $this->method;
	}
	
	/**
	 * @return \CudiBundle\Entity\Articles\Discount\Type
	 */
	public function getType()
	{
	    return $this->type;
	}
	
	/**
	 * @return integer
	 */
	public function getArticlePrice()
	{
	    if ($this->method == 'percentage') 
	        return $this->getArticle()->getSellPrice() * (1 - $this->value/100);
	    else
	        return $this->getArticle()->getSellPrice() - $this->value;
	}
}