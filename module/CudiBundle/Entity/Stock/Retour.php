<?php

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Retour")
 * @ORM\Table(
 *    name="cudi_stock_retours",
 *    indexes={@ORM\Index(name="cudi_stock_retours_timestamp", columns={"timestamp"})}
 * )
 */
class Retour
{
    /**
     * @var integer The ID of the retour
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time the retour item was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var integer The number of the retour
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var Article The article of the retour
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Person The person of the retour
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The comment of the retour
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @param Article $article The article of the retour
     * @param integer $number  The number of the retour
     * @param Person  $person  The person of the retour
     * @param string  $comment The comment of the retour
     */
    public function __construct(Article $article, $number, Person $person, $comment)
    {
        $this->article = $article;
        $this->person = $person;
        $this->number = $number;
        $this->comment = $comment;
        $this->timestamp = new DateTime();
        $article->addStockValue(-$number);
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
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->article->getPurchasePrice() * $this->number;
    }
}
