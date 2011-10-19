<?php

namespace Litus\Entity\Cudi\Articles\StockArticles;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\StockArticles\Internal")
 * @Table(name="cudi.articles_stockarticles_internal")
 */
class Internal extends \Litus\Entity\Cudi\Articles\Stock
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Articles\StockArticles\Binding")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Articles\StockArticles\Color")
	 * @JoinColumn(name="front_page_color", referencedColumnName="id")
	 */
	private $frontPageColor;
	
	public function __construct(
		$title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, 
		$barcode, $bookable, $unbookable, $supplier, $canExpire, $nbBlackAndWhite, $nbColored, $binding, $official, $rectoverso, $frontPageColor
	) {
		parent::__construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, $supplier, $canExpire);
		
		$this->nbBlackAndWhite = $nbBlackAndWhite;
		$this->nbColored = $nbColored;
		$this->binding = $binding;
		$this->official = $official;
		$this->rectoVerso = $rectoverso;
		$this->frontPageColor = $frontPageColor;
	}
}
