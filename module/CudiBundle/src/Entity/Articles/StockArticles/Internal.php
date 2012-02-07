<?php

namespace CudiBundle\Entity\Articles\StockArticles;

use Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\StockArticles\Internal")
 * @Table(name="cudi.articles_stockarticles_internal")
 */
class Internal extends \CudiBundle\Entity\Articles\Stock
{
	/**
	 * @Column(name="nb_black_and_white", type="smallint")
	 */
	private $nbBlackAndWhite;
	
	/**
	 * @Column(name="nb_colored", type="smallint")
	 */
	private $nbColored;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\StockArticles\Binding")
	 * @JoinColumn(name="binding", referencedColumnName="id")
	 */
	private $binding;
	
	/**
	 * @Column(type="boolean")
	 */
	private $official;
	
	/**
	 * @Column(name="recto_verso", type="boolean")
	 */
	private $rectoVerso;
	
	/**
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Articles\StockArticles\Color")
	 * @JoinColumn(name="front_page_color", referencedColumnName="id")
	 */
	private $frontPageColor;
	
	/**
	 * @Column(name="front_page_text_colored", type="boolean")
	 */
	private $frontPageTextColored;
	
	public function __construct(
		$title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, 
		$barcode, $bookable, $unbookable, $supplier, $canExpire, $nbBlackAndWhite, $nbColored, $binding, $official, $rectoverso, $frontPageColor, $frontPageTextColored
	) {
		parent::__construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, $supplier, $canExpire);
		
		$this->setNbBlackAndWhite($nbBlackAndWhite)
			->setNbColored($nbColored)
			->setBinding($binding)
			->setIsOfficial($official)
			->setIsRectoVerso($rectoverso)
			->setFrontColor($frontPageColor)
			->setFrontPageTextColored($frontPageTextColored);
	}
	
	/**
	 * @return boolean
	 */
	public function isInternal()
	{
		return true;
	}
	
	/**
	 * @return int
	 */
	public function getNbBlackAndWhite()
	{
		return $this->nbBlackAndWhite;
	}
	
	/**
	 * @param int $nbBlackAndWhite
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setNbBlackAndWhite($nbBlackAndWhite)
	{
		$this->nbBlackAndWhite = $nbBlackAndWhite;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getNbColored()
	{
		return $this->nbColored;
	}
	
	/**
	 * @param int $nbColored
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setNbColored($nbColored)
	{
		$this->nbColored = $nbColored;
		return $this;
	}
	
	/**
	 * @return CudiBundle\Entity\Articles\StockArticle\Binding
	 */
	public function getBinding()
	{
		return $this->binding;
	}
	
	/**
	 * @param CudiBundle\Entity\Articles\StockArticles\Binding $binding
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setBinding($binding)
	{
		$this->binding = $binding;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isOfficial()
	{
		return $this->official;
	}
	
	/**
	 * @param boolean $official
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setIsOfficial($official)
	{
		$this->official = $official;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isRectoVerso()
	{
		return $this->rectoVerso;
	}
	
	/**
	 * @param boolean $rectoVerso
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setIsRectoVerso($rectoVerso)
	{
		$this->rectoVerso = $rectoVerso;
		return $this;
	}
	
	/**
	 * @return CudiBundle\Entity\Articles\StockArticle\Color
	 */
	public function getFrontColor()
	{
		return $this->frontPageColor;
	}
	
	/**
	 * @param CudiBundle\Entity\Articles\StockArticles\Color $frontPageColor
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setFrontColor($frontPageColor)
	{
		$this->frontPageColor = $frontPageColor;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getFrontPageTextColored()
	{
		return $this->frontPageTextColored;
	}
	
	/**
	 * @param boolean $frontPageTextColored
	 *
	 * @return CudiBundle\Entity\Articles\StockArticles\Internal
	 */
	public function setFrontPageTextColored($frontPageTextColored)
	{
		$this->frontPageTextColored = $frontPageTextColored;
	}
	
	/**
	 * @return integer
	 */
	public function getNbPages()
	{
		return $this->getNbBlackAndWhite() + $this->getNbColored();
	}
	
	public function getFiles(EntityManager $entityManager)
	{
		return $entityManager
			->getRepository('CudiBundle\Entity\File')
			->findAllByArticle($this);
	}
}
