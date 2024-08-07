<?php

namespace SyllabusBundle\Entity\Subject;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject\Comment")
 * @ORM\Table(name="syllabus_subjects_comments")
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
     * @var Subject The subject of the comment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject", referencedColumnName="id")
     */
    private $subject;

    /**
     * @var string The type of the comment
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var Person|null Flags whether this comment was read
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="read_by", referencedColumnName="id")
     */
    private $readBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\Subject\Reply", mappedBy="comment", cascade={"remove"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $replies;

    /**
     * @var array The possible types of a comment
     */
    private static $possibleTypes = array(
        'external', 'internal',
    );

    /**
     * @throws InvalidArgumentException
     * @param  Person  $person  The person that created the comment
     * @param  Subject $subject The subject of the comment
     * @param  string  $text    The content of the comment
     * @param  string  $type    The type of the comment
     */
    public function __construct(Person $person, Subject $subject, $text = '', $type = 'internal')
    {
        $this->person = $person;
        $this->text = $text;
        $this->date = new DateTime();
        $this->subject = $subject;

        if (!self::isValidCommentType($type)) {
            throw new InvalidArgumentException('The comment type is not valid.');
        }
        $this->type = $type;

        $this->replies = new ArrayCollection();
    }

    /**
     * @param  string $type
     * @return boolean
     */
    public static function isValidCommentType($type)
    {
        return in_array($type, self::$possibleTypes);
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
     * @return Comment
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
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
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
     * @return Comment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRead()
    {
        return $this->readBy !== null;
    }

    /**
     * @param  Person|null $readBy
     * @return Comment
     */
    public function setReadBy(Person $readBy = null)
    {
        $this->readBy = $readBy;

        return $this;
    }

    /**
     * @return Person|null
     */
    public function getReadBy()
    {
        return $this->readBy;
    }

    /**
     * @return ArrayCollection
     */
    public function getReplies()
    {
        return $this->replies;
    }
}
