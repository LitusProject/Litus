<?php

namespace CudiBundle\Entity;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\ArticleSubjectMap")
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
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @OneToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(name="subject", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
