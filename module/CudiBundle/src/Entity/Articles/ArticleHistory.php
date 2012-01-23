<?php

namespace Litus\Entity\Cudi\Articles;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Articles\ArticleHistory")
 * @Table(name="cudi.articles_articlehistory")
 */
class ArticleHistory
{
    /**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

	/**
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
     * @JoinColumn(name="precursor", referencedColumnName="id")
	 */
	private $precursor;
	
	public function __construct($article, $precursor)
	{
		$article->setVersionNumber($precursor->getVersionNumber()+1);
		$precursor->setIsBookable(false);
		
		$order = Registry::get(DoctrineResource::REGISTRY_KEY)
		    ->getRepository('Litus\Entity\Cudi\Stock\OrderItem')
		    ->findOneOpenByArticle($precursor);
		if (null !== $order)
			$order->setArticle($article);
		
		$bookings = Registry::get(DoctrineResource::REGISTRY_KEY)
		    ->getRepository('Litus\Entity\Cudi\Sales\Booking')
		    ->findAllBookedByArticle($precursor);
		foreach($bookings as $booking)
			$booking->setArticle($article);
		
		$this->article = $article;
		$this->precursor = $precursor;
	}
}