<?php

namespace Litus\Entities\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\ArticleSubjectMapRepository")
 * @Table(name="cudi.article_subject_map")
 */
class ArticleSubjectMap
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
	 * @OneToOne(targetEntity="Litus\Entities\Syllabus\Subject")
	 * @JoinColumn(name="subject_id", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
