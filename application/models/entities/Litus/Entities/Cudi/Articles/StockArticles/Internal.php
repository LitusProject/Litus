<?php

namespace Litus\Entities\Cudi\Articles\StockArticles;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Articles\StockArticles\Internal")
 * @Table(name="cudi.articles_stockarticles_internal")
 */
class Internal extends \Litus\Entities\Cudi\Articles\Stock
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
	 * @TODO Column(type="")
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
	 * @TODO Column(name="front_page_color", type="")
	 */
	private $frontPageColor;
	
	public function __construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, 
			$barcode, $bookable, $unbookable, $nrbwpages, $nrcolorpages, $official, $rectoverso) {
		parent::__construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable);
		
		$this->nbBlackAndWhite = $nrbwpages;
		$this->nbColored = $nrcolorpages;
		$this->official = $official;
		$this->rectoVerso = $rectoverso;
		
	}
}
