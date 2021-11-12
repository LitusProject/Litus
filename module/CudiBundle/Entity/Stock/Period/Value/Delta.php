<?php

namespace CudiBundle\Entity\Stock\Period\Value;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Period;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Period\Value\Delta")
 * @ORM\Table(name="cudi_stock_periods_values_deltas")
 */
class Delta
{
    /**
     * @var integer The ID of the delta
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time of the delta
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var Person The person who added the delta
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var integer The value of the delta
     *
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @var Article The article of the delta
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Period The period of the delta
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Stock\Period")
     * @ORM\JoinColumn(name="period", referencedColumnName="id")
     */
    private $period;

    /**
     * @var string The comment of the delta
     *
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @param Person  $person  The person who added the delta
     * @param Article $article The article of the delta
     * @param Period  $period  The period of the delta
     * @param integer $value   The value of the delta
     * @param string  $comment The comment of the delta
     */
    public function __construct(Person $person, Article $article, Period $period, $value, $comment)
    {
        $this->person = $person;
        $this->timestamp = new DateTime();
        $this->article = $article;
        $this->period = $period;
        $this->value = $value;
        $this->comment = $comment;
    }

    /**
     * Get the id of the delta
     *
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
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getSummary($length = 50)
    {
        return substr($this->comment, 0, $length) . (strlen($this->comment) > $length ? '...' : '');
    }
}
