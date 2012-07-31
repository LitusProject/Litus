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

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Comments\Comment")
 * @Table(name="cudi.comments_comment")
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
     * @var string The type of the comment
     *
     * @Column(type="string")
     */
    private $type;
    
    /**
     * @var array The possible types of a comment
     */
    private static $POSSIBLE_TYPES = array(
        'external', 'internal'
    );
    
    /**
     * @throws \InvalidArgumentException
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\Users\Person $person The person that created the comment
     * @param \CudiBundle\Entity\Article $article The article of the comment
     * @param string $text The content of the comment
     * @param string $type The type of the comment
     */
    public function __construct(EntityManager $entityManager, Person $person, Article $article, $text, $type) {
        $this->person = $person;
        $this->text = $text;
        $this->date = new \DateTime();
        
        $entityManager->persist(new Mapping($article, $this));
        
        if (!self::isValidCommentType($type))
            throw new \InvalidArgumentException('The comment type is not valid.');
        $this->type = $type;
    }
    
    /**
     * @return boolean
     */
    public static function isValidCommentType($type)
    {
        return in_array($type, self::$POSSIBLE_TYPES);
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
    public function getSummary($length = 50)
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
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
