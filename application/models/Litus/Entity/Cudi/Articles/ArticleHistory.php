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
     * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
     * @JoinColumn(name="precursor_id", referencedColumnName="id")
	 */
	private $precursor;
}