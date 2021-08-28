<?php

namespace CudiBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log")
 * @ORM\Table(name="cudi_log")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "sales_assignments"="CudiBundle\Entity\Log\Sale\Assignments",
 *      "sales_cancellations"="CudiBundle\Entity\Log\Sale\Cancellations",
 *      "sales_prof_version"="CudiBundle\Entity\Log\Sale\ProfVersion",
 *      "articles_sales_bookable"="CudiBundle\Entity\Log\Article\Sale\Bookable",
 *      "articles_sales_unbookable"="CudiBundle\Entity\Log\Article\Sale\Unbookable",
 *      "articles_subject_map_added"="CudiBundle\Entity\Log\Article\SubjectMap\Added",
 *      "articles_subject_map_removed"="CudiBundle\Entity\Log\Article\SubjectMap\Removed"
 * })
 */
abstract class Log
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
     * @var DateTime The time of the log
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var Person The person of the log
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
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
     * @param Person $person
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'log';
    }
}
