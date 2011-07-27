<?php

namespace Litus\Entities\Cudi\Articles;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Articles\ArticleHistoryRepository")
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
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Article")
     * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Article")
     * @JoinColumn(name="precursor_id", referencedColumnName="id")
	 */
	private $precursor;
}