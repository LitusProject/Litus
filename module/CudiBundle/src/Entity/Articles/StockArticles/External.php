<?php

namespace CudiBundle\Entity\Articles\StockArticles;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\StockArticles\External")
 * @Table(name="cudi.articles_stockarticles_external")
 */
class External extends \CudiBundle\Entity\Articles\Stock
{
	/**
	 * @return boolean
	 */
	public function isInternal()
	{
		return false;
	}
}
