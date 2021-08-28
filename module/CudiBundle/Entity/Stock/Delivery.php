<?php

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Delivery")
 * @ORM\Table(
 *     name="cudi_stock_deliveries",
 *     indexes={@ORM\Index(name="cudi_stock_deliveries_timestamp", columns={"timestamp"})}
 * )
 */
class Delivery
{
    /**
     * @var integer The ID of the delivery item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Article The article of the delivery
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var DateTime The time of the delivery
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var integer The number of the delivery
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var Person The person who ordered the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @param Article $article The article of the delivery
     * @param integer $number  The number of the article
     * @param Person  $person  The person who ordered the order
     */
    public function __construct(Article $article, $number, Person $person)
    {
        $this->article = $article;
        $this->timestamp = new DateTime();
        $this->number = $number;
        $this->person = $person;
        $article->addStockValue($number);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
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
    public function getPrice()
    {
        return $this->article->getPurchasePrice() * $this->number;
    }
}
