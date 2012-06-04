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

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Retour")
 * @Table(name="cudi.sales_retour", indexes={@index(name="sales_retour_time", columns={"timestamp"})})
 */
class Retour
{
	/**
	 * @var integer The ID of the retour
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \DateTime The time the retour item was created
	 *
	 * @Index
	 * @Column(type="datetime")
	 */
	private $timestamp;
	
	/**
	 * @var \CudiBundle\Entity\Sales\Article The article of the retour
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person of the retour
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @var string The comment of the retour
	 *
	 * @Column(type="text", nullable=true)
	 */
	private $comment;
	
	/**
	 * @param \CudiBundle\Entity\Sales\Article $article The article of the retour
	 * @param \CommonBundle\Entity\Users\Person $person The person of the retour
	 * @param string $comment The comment of the retour
	 */
	public function __construct(Article $article, Person $person, $comment)
	{
	    $this->article = $article;
	    $this->person = $person;
	    $this->comment = $comment;
	    $this->timestamp = new DateTime();
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
	    return $this->id;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getTimestamp()
	{
	    return $this->timestamp;
	}
	
	/**
	 * @return \CudiBundle\Entity\Sales\Article
	 */
	public function getArticle()
	{
	    return $this->article;
	}
	
	/**
	 * @return \CommonBundle\Entity\Users\person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}
}
