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

use CudiBundle\Entity\Articles\MetaInfo,
	CudiBundle\Entity\Articles\StockArticles\Binding,
	CudiBundle\Entity\Articles\StockArticles\Color,
	CudiBundle\Entity\Supplier,
	Doctrine\ORM\EntityManager;

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
	
	/**
     * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param string $title The title of the article
	 * @param CudiBundle\Entity\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
	 * @param float $purchasePrice The purchase price of this article.
	 * @param float $sellPrice The sell price of this article for non-members.
	 * @param float $sellPriceMembers The sell price of this article for members.
	 * @param integer $barcode This article's barcode.
	 * @param boolean $bookable Indicates whether the article can be booked.
	 * @param boolean $unbookable Indicates whether the article can be unbooked.
	 * @param CudiBundle\Entity\Supplier $supplier The supplier of the stock item.
	 * @param boolean $canExpire Whether the article can expire.
	 * @param integer $nbBlackAndWhite
	 * @param integer $nbColored
	 * @param CudiBundle\Entity\Articles\StockArticles\Binding $binding
	 * @param boolean $official
	 * @param boolean $rectoverso
	 * @param CudiBundle\Entity\Articles\StockArticle\Color $frontPageColor
	 * @param boolean $frontPageTextColored
	 */
	public function __construct(
		EntityManager $entityManager, $title, MetaInfo $metaInfo, $purchasePrice, $sellPrice, $sellPriceMembers, 
		$barcode, $bookable, $unbookable, Supplier $supplier, $canExpire, $nbBlackAndWhite, $nbColored, Binding $binding, $official, $rectoverso, Color $frontPageColor, $frontPageTextColored
	) {
		parent::__construct($entityManager, $title, $metaInfo, $purchasePrice, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, $supplier, $canExpire);
		
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
	 * @param integer $nbBlackAndWhite
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
	 * @param integer $nbColored
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
	public function setBinding(Binding $binding)
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
	public function setFrontColor(Color $frontPageColor)
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
