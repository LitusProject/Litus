<?php

namespace Litus\Entity\Cudi\Articles;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\Stub")
 * @Table(name="cudi.articles_stub")
 */
class Stub extends \Litus\Entity\Cudi\Article
{
	/**
	 * @return boolean
	 */
	public function isInternal()
	{
		return false;
	}
	
	/**
	 * @return boolean
	 */
	public function isStock()
	{
		return false;
	}
}
