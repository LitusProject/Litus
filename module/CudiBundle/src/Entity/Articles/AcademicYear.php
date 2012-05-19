<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Entity\Articles;

use \CommonBundle\Entity\General\AcademicYear,
    \CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\AcademicYear")
 * @Table(name="cudi.articles_academic_year")
 */
class AcademicYear
{
    /**
     * @var integer The ID of the mapping
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
     * @var \CudiBundle\Entity\Article The article of the mapping
     *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @var \CommonBundle\Entity\General\AcademicYear The year of the mapping
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
	 * @JoinColumn(name="academic_year", referencedColumnName="id")
	 */
	private $academicYear;
    
    /**
     * @param \CudiBundle\Entity\Article $article
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     */
    public function __construct(Article $article, AcademicYear $academicYear)
    {
        $this->article = $article;
        $this->academicYear = $academicYear;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \CudiBundle\Entity\Articles\Internal
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
