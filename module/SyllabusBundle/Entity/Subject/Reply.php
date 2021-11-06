<?php

namespace SyllabusBundle\Entity\Subject;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject\Reply")
 * @ORM\Table(name="syllabus_subjects_replies")
 */
class Reply
{
    /**
     * @var integer The ID of the reply
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time the reply was created
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var string The content of the reply
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var Person The person that created the reply
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Comment The comment of the reply
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject\Comment", inversedBy="replies")
     * @ORM\JoinColumn(name="comment", referencedColumnName="id")
     */
    private $comment;

    /**
     * @param Person  $person  The person that created the reply
     * @param Comment $comment The comment of the reply
     * @param string  $text    The content of the reply
     */
    public function __construct(Person $person, Comment $comment, $text = '')
    {
        $this->person = $person;
        $this->text = $text;
        $this->date = new DateTime();
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
     * @return Reply
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
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return \SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->comment->getSubject();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->comment->getType();
    }
}
