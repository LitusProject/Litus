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
 
namespace CudiBundle\Entity\Stock\PeriodValues;

use CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Stock\Period;
    
/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\PeriodValues\Delivery")
 * @Table(name="cudi.stock_period_values_delivery")
 */
class Delivery
{
	/**
	 * @var integer The ID of the value
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var integer The value of the value
	 *
	 * @Column(type="integer")
	 */
	private $value;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Article The article of the value
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CudiBundle\Entity\Stock\Period The period of the value
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Stock\Period")
	 * @JoinColumn(name="period", referencedColumnName="id")
	 */
	private $period;
	
	/**
	 * @param \CudiBundle\Entity\Sales\Article $stockItem The article of the value
	 * @param \CudiBundle\Entity\Stock\Period $period The period of the value
	 * @param integer $value The value of the value
	 */
	public function __construct(Article $article, Period $period, $value)
	{
		$this->article = $article;
		$this->period = $period;
		$this->value = $value;
	}
	
	/**
	 * Get the id of the value
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return integer
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\Article
	 */
	public function getArticle()
	{
		return $this->article;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\Period
	 */
	public function getPeriod()
	{
		return $this->period;
	}
}
