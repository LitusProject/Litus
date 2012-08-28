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

namespace SyllabusBundle\Entity\Subject;

use CommonBundle\Entity\Users\Person,
    SyllabusBundle\Entity\Subject,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject\Comment")
 * @ORM\Table(name="syllabus.subjects_comments")
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
     * @var \CommonBundle\Entity\Users\Person The person that created the comment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \SyllabusBundle\Entity\Subject The subject of the comment
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
     * @param \SyllabusBundle\Entity\Subject $subject The subject of the comment
     * @param string $text The content of the comment
     * @param string $type The type of the comment
     */
    public function __construct(EntityManager $entityManager, Person $person, Subject $subject, $text, $type) {
        $this->person = $person;
        $this->text = $text;
        $this->date = new DateTime();
        $this->subject = $subject;

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
     * @return \SyllabusBundle\Entity\Subject
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
}
