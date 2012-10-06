<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Entity;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log")
 * @ORM\Table(name="cudi.log")
 */
class Log
{
    /**
     * @var integer The ID of the log
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time of the log
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CommonBundle\Entity\Users\Person The person of the log
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The type of the log
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The text of the log
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $type
     * @param string $text
     */
    public function __construct(Person $person, $type, $text)
    {
        $this->person = $person;
        $this->type = $type;
        $this->text = $text;
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

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return integer
     */
    public function getSizeOfArray()
    {
        return count(unserialize($this->text));
    }
}
