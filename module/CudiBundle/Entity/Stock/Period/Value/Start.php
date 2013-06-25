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

namespace CudiBundle\Entity\Stock\Period\Value;

use CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Period\Value\Start")
 * @ORM\Table(name="cudi.stock_periods_values_starts")
 */
class Start
{
    /**
     * @var integer The ID of the value
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The value of the value
     *
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @var \CudiBundle\Entity\Sale\Article The article of the value
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CudiBundle\Entity\Stock\Period The period of the value
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Stock\Period")
     * @ORM\JoinColumn(name="period", referencedColumnName="id")
     */
    private $period;

    /**
     * @param \CudiBundle\Entity\Sale\Article $stockItem The article of the value
     * @param \CudiBundle\Entity\Stock\Period $period The period of the value
     * @param integer $value The value of the value
     */
    public function __construct(Article $article, Period $period, $value)
    {
        $this->article = $article;
        $this->period = $period;
        $this->value = $value;
    }

    /**
     * Get the id of the value
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
}
