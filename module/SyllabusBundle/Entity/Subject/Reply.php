<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Entity\Subject;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject\Reply")
 * @ORM\Table(name="syllabus.subjects_reply")
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
     * @var \DateTime The time the reply was created
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
     * @var \CommonBundle\Entity\User\Person The person that created the reply
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \SyllabusBundle\Entity\Comment The comment of the reply
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject\Comment", inversedBy="replies")
     * @ORM\JoinColumn(name="comment", referencedColumnName="id")
     */
    private $comment;

    /**
     * @throws \InvalidArgumentException
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\User\Person $person The person that created the reply
     * @param \SyllabusBundle\Entity\Subject\Comment $comment The comment of the reply
     * @param string $text The content of the reply
     */
    public function __construct(EntityManager $entityManager, Person $person, Comment $comment, $text) {
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
     * @return \SyllabusBundle\Entity\Subject\Comment
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
        return $this->type;
    }
}
