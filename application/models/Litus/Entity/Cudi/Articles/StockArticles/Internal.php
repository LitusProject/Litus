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
}
