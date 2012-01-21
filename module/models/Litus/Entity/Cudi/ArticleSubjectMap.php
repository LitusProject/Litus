<?php

namespace Litus\Entity\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\ArticleSubjectMap")
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
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @OneToOne(targetEntity="Litus\Entity\Syllabus\Subject")
	 * @JoinColumn(name="subject", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
