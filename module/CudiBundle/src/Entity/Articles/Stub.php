<?php

namespace CudiBundle\Entity\Articles;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\Stub")
 * @Table(name="cudi.articles_stub")
 */
class Stub extends \CudiBundle\Entity\Article
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
