<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Stock\Period\Value;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Period\Value\Delta")
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
     * @var \CommonBundle\Entity\User\Person The person who added the delta
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
     * @var \CudiBundle\Entity\Sale\Article The article of the delta
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
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
     * @param \CommonBundle\Entity\User\Person $person  The person who added the delta
     * @param \CudiBundle\Entity\Stock\Period  $period  The period of the delta
     * @param integer                          $value   The value of the delta
     * @param string                           $comment The comment of the delta
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
     * @return \CommonBundle\Entity\User\Person
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
     * @return \CudiBundle\Entity\Sale\Article
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
