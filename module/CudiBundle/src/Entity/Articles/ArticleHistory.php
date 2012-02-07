<?php

namespace CudiBundle\Entity\Articles;

use Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\ArticleHistory")
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
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article")
     * @JoinColumn(name="precursor", referencedColumnName="id")
	 */
	private $precursor;
	
	public function __construct(EntityManager $entityManager, $article, $precursor)
	{
		$article->setVersionNumber($precursor->getVersionNumber()+1);
		$precursor->setIsBookable(false);
		
		$order = $entityManager
		    ->getRepository('CudiBundle\Entity\Stock\OrderItem')
		    ->findOneOpenByArticle($precursor);
		if (null !== $order)
			$order->setArticle($article);
		
		$bookings = $entityManager
		    ->getRepository('CudiBundle\Entity\Sales\Booking')
		    ->findAllBookedByArticle($precursor);
		foreach($bookings as $booking)
			$booking->setArticle($article);
		
		$this->article = $article;
		$this->precursor = $precursor;
	}
}