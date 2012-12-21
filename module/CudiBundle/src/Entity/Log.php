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
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "sales_assignments"="CudiBundle\Entity\Log\Sales\Assignments",
 *      "sales_prof_version"="CudiBundle\Entity\Log\Sales\ProfVersion"
 * })
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
     * @var string The text of the log
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $text
     */
    public function __construct(Person $person, $text)
    {
        $this->person = $person;
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
    public function getText()
    {
        return $this->text;
    }
}
