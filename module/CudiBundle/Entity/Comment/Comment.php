<?php

namespace CudiBundle\Entity\Comment;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Article;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Comment\Comment")
 * @ORM\Table(name="cudi_comments_comments")
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
     * @var DateTime The time the comment was created
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
     * @var Person The person that created the comment
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
    public static $possibleTypes = array(
        'internal' => 'Internal',
        'external' => 'External',
        'site'     => 'Site',
    );

    /**
     * @throws InvalidArgumentException
     *
     * @param EntityManager $entityManager
     * @param Person        $person        The person that created the comment
     * @param Article       $article       The article of the comment
     * @param string        $text          The content of the comment
     * @param string|null   $type          The type of the comment
     */
    public function __construct(EntityManager $entityManager, Person $person, Article $article, $text = '', $type = null)
    {
        $this->person = $person;
        $this->date = new DateTime();

        $entityManager->persist(new ArticleMap($article, $this));

        $this->setText($text);
        if ($type !== null) {
            $this->setType($type);
        }
    }

    /**
     * @param  string $type
     * @return boolean
     */
    public static function isValidCommentType($type)
    {
        return array_key_exists($type, self::$possibleTypes);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
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
     * @param  string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary($length = 50)
    {
        return substr($this->text, 0, $length) . (strlen($this->text) > $length ? '...' : '');
    }

    /**
     * @return Person
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

    /**
     * @param  string $type
     * @return self
     */
    public function setType($type)
    {
        if (!self::isValidCommentType($type)) {
            throw new InvalidArgumentException('The comment type is not valid.');
        }
        $this->type = $type;

        return $this;
    }
}
