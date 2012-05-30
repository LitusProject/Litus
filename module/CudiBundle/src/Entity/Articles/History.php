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

use CudiBundle\Entity\Article,
    CudiBundle\Entity\Articles\SubjectMap as SubjectMapping,
    CudiBundle\Entity\Comments\Mapping as CommentMapping,
    CudiBundle\Entity\Files\Mapping as FileMapping,
	Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\History")
 * @Table(name="cudi.articles_history")
 */
class History
{
    /**
     * @var integer The ID of this article history
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

	/**
	 * @var \CudiBundle\Entity\Article The newest version of the two
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CudiBundle\Entity\Article The oldest version of the two
	 *
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article", cascade={"persist"})
     * @JoinColumn(name="precursor", referencedColumnName="id")
	 */
	private $precursor;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The entitymanager
	 * @param \CudiBundle\Entity\Article $article The new version of the article
	 * @param \CudiBundle\Entity\Article $precursor The old version of the article
	 */
	public function __construct(EntityManager $entityManager, Article $article, Article $precursor = null)
	{
	    $this->precursor = isset($precursor) ? $precursor : $article->duplicate();

	    $this->precursor->setVersionNumber($article->getVersionNumber())
	        ->setIsHistory(true);
	    
		$article->setVersionNumber($article->getVersionNumber()+1);
		
		$this->article = $article;
	}
}