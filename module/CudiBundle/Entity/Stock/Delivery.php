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

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Delivery")
 * @ORM\Table(name="cudi.stock_deliveries", indexes={@ORM\Index(name="stock_deliveries_time", columns={"timestamp"})})
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
     * @var \CudiBundle\Entity\Sales\Article The article of the delivery
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \DateTime The time of the delivery
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
     * @var \CommonBundle\Entity\Users\Person The person who ordered the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @param \CudiBundle\Entity\Sales\Article $article The article of the delivery
     * @param integer $number The number of the article
     * @param \CommonBundle\Entity\Users\Person The person who ordered the order
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
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \DateTime
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
     * @return \CommonBundle\Entity\Users\Person
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
