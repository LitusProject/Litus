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
 
namespace CudiBundle\Entity\Comments;

use CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Comments\Mapping")
 * @Table(name="cudi.commentss_mapping")
 */
class Mapping
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
	 * @var \CudiBundle\Entity\Comments\Comment The comment of the mapping
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Comments\Comment")
	 * @JoinColumn(name="comment", referencedColumnName="id")
	 */
	private $comment;
    
    /**
     * @param \CudiBundle\Entity\Article $article
     * @param \CudiBundle\Entity\Comments\Comment $file
     */
    public function __construct(Article $article, Comment $comment)
    {
        $this->article = $article;
        $this->comment = $comment;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return \CudiBundle\Entity\Comments\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
