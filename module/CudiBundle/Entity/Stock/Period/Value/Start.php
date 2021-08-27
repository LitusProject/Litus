<?php

namespace CudiBundle\Entity\Stock\Period\Value;

use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Period;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Period\Value\Start")
 * @ORM\Table(name="cudi_stock_periods_values_starts")
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
     * @var Article The article of the value
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Period The period of the value
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Stock\Period")
     * @ORM\JoinColumn(name="period", referencedColumnName="id")
     */
    private $period;

    /**
     * @param Article $article The article of the value
     * @param Period  $period  The period of the value
     * @param integer $value   The value of the value
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
}
