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

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Articles\Comment")
 * @Table(name="cudi.articles_comment")
 */
class Comment
{
	/**
	 * @var integer The ID of the comment
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\Article The article of this comment
	 * 
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \DateTime The time the comment was created
	 * 
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @var string The content of the comment
	 *
	 * @Column(type="text")
	 */
	private $text;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person that created the comment
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="person", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @param \CommonBundle\Entity\Users\Person $person
	 * @param \CudiBundle\Entity\Article $article
	 * @param string $text
	 */
	public function __construct(Person $person, Article $article, $text) {
		$this->person = $person;
		$this->article = $article;
		$this->text = $text;
		$this->date = new \DateTime();
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
	 * @return \DateTime
	 */
	public function getDate()
	{
	    return $this->date;
	}
	
	/**
	 * @return string
	 */
	public function getText()
	{
	    return $this->text;
	}
	
	/**
	 * @return string
	 */
	public function getSummary($length = 20)
	{
	    return substr($this->text, 0, $length) . (strlen($this->text) > $length ? '...' : '');
	}
	
	/**
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getPerson()
	{
	    return $this->person;
	}
}