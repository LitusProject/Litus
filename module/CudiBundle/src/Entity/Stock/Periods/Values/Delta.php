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

namespace CudiBundle\Entity\Stock\Periods\Values;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Stock\Period,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Periods\Values\Delta")
 * @ORM\Table(name="cudi.stock_periods_values_deltas")
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
     * @var \DateTime The time of the delta
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CommonBundle\Entity\Users\Person The person who added the delta
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
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
     * @var \CudiBundle\Entity\Sales\Article The article of the delta
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CudiBundle\Entity\Stock\Period The period of the delta
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
     * @param \CommonBundle\Entity\Users\Person $person The person who added the delta
     * @param \CudiBundle\Entity\Sales\Article $stockItem The article of the delta
     * @param \CudiBundle\Entity\Stock\Period $period The period of the delta
     * @param integer $value The value of the delta
     * @param string $comment The comment of the delta
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
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \CudiBundle\Entity\Stock\Period
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
