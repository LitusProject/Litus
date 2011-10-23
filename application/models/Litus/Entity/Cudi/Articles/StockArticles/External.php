<?php

namespace Litus\Entity\Cudi\Articles\StockArticles;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\StockArticles\External")
 * @Table(name="cudi.articles_stockarticles_external")
 */
class External extends \Litus\Entity\Cudi\Articles\Stock
{
	/**
	 * @return boolean
	 */
	public function isInternal()
	{
		return false;
	}
}
