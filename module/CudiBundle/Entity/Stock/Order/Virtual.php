<?php

namespace CudiBundle\Entity\Stock\Order;

use CudiBundle\Entity\Sale\Article;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Order\Virtual")
 * @ORM\Table(name="cudi_stock_orders_virtual")
 */
class Virtual
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Article The article of the item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer The number of items ordered
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var DateTime The time the order was created
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @param Article $article The article of the item
     * @param integer $number  The number of items ordered
     */
    public function __construct(Article $article, $number)
    {
        $this->article = $article;
        $this->number = $number;
        $this->dateCreated = new DateTime();
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
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}
