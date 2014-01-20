<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Comment;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Article,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Comment\Comment")
 * @ORM\Table(name="cudi.comments_comments")
 */
class Comment
{
    /**
     * @var integer The ID of the comment
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time the comment was created
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var string The content of the comment
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var \CommonBundle\Entity\User\Person The person that created the comment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The type of the comment
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var array The possible types of a comment
     */
    public static $POSSIBLE_TYPES = array(
        'internal' => 'Internal',
        'external' => 'External',
        'site' => 'Site',
    );

    /**
     * @throws \InvalidArgumentException
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\User\Person $person The person that created the comment
     * @param \CudiBundle\Entity\Article $article The article of the comment
     * @param string $text The content of the comment
     * @param string $type The type of the comment
     */
    public function __construct(EntityManager $entityManager, Person $person, Article $article, $text, $type) {
        $this->person = $person;
        $this->text = $text;
        $this->date = new DateTime();

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
        return array_key_exists($type, self::$POSSIBLE_TYPES);
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
     * @return \CommonBundle\Entity\User\Person
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
