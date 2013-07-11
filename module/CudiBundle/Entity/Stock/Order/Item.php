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

namespace CudiBundle\Entity\Stock\Order;

use CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Order\Item")
 * @ORM\Table(name="cudi.stock_orders_items")
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
     * @var \CudiBundle\Entity\Sale\Article The article of the item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CudiBundle\Entity\Stock\Order\Order The order of the item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Stock\Order\Order", inversedBy="items")
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
     * @param \CudiBundle\Entity\Sale\Article $article The article of the item
     * @param \CudiBundle\Entity\Stock\Order\Order $order The order of the item
     * @param integer $number The number of items ordered
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
     * @return \CudiBundle\Entity\Stock\Order\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article
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
     * @return \CudiBundle\Entity\Stock\Order\Item
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
