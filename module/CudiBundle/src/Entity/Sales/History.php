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
 
namespace CudiBundle\Entity\Sales;

use CudiBundle\Entity\Sales\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\History")
 * @Table(name="cudi.sales_history")
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
	 * @var \CudiBundle\Entity\Sales\Article The newest version of the two
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Article The oldest version of the two
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article", cascade={"persist"})
     * @JoinColumn(name="precursor", referencedColumnName="id")
	 */
	private $precursor;
	
	/**
	 * @param \CudiBundle\Entity\Sales\Article $article The new version of the article
	 * @param \CudiBundle\Entity\Sales\Article $precursor The old version of the article
	 */
	public function __construct(Article $article, Article $precursor = null)
	{
	    $this->precursor = isset($precursor) ? $precursor : $article->duplicate();

	    $this->precursor->setVersionNumber($article->getVersionNumber())
	        ->setIsHistory(true);
	    
		$article->setVersionNumber($article->getVersionNumber()+1);
		
		$this->article = $article;
	}
}