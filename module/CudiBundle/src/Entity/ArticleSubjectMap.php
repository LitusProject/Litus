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
 
namespace CudiBundle\Entity;

use SyllabusBundle\Entity\Subject;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\ArticleSubjectMap")
 * @Table(name="cudi.article_subject_map")
 */
class ArticleSubjectMap
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
	 * @var \SyllabusBundle\Entity\Subject The subject of the mapping
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(name="subject", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @var boolean Flag whether the article is mandatory
     *
     * @Column(type="boolean")
     */
    private $mandatory;
    
    /**
     * @var boolean The flag whether the mapping is removed
     *
     * @Column(type="boolean")
     */
    private $removed = false;
    
    /**
     * @var boolean The flag whether the article is enabled (for in ProfBundle)
     *
     * @Column(type="boolean")
     */
    private $enabled = true;
    
    /**
     * @param \CudiBundle\Entity\Article $article
     * @param \SyllabusBundle\Entity\Subject $subject
     * @param boolean $mandatory
     */
    public function __construct(Article $article, Subject $subject, $mandatory)
    {
        $this->article = $article;
        $this->subject = $subject;
        $this->mandatory = $mandatory;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }
    
    /**
     * @param boolean $removed Whether this item is removed or not
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setRemoved($removed = true)
    {
    	$this->removed = $removed;
    	return $this;
    }
    
    /**
     * @param boolean
     * @return \CudiBundle\Entity\ArticleSubjectMap
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
