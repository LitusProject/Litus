<?php

namespace CudiBundle\Entity\Stock\Order;

use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Order\Item")
 * @ORM\Table(name="cudi_stock_orders_items")
 */
class Item
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
     * @var Order The order of the item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Stock\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var integer The number of items ordered
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @param Article $article The article of the item
     * @param Order   $order   The order of the item
     * @param integer $number  The number of items ordered
     */
    public function __construct(Article $article, Order $order, $number)
    {
        $this->article = $article;
        $this->order = $order;
        $this->setNumber($number);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
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
     * @param integer $number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->article->getPurchasePrice() * $this->number;
    }
}
