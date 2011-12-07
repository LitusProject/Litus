<?php

namespace Litus\Entity\Cudi\Articles;

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
		$this->article = $article;
		$this->precursor = $precursor;
	}
}