<?php

namespace BrBundle\Entity\Product\Order;

use BrBundle\Entity\Product;
use BrBundle\Entity\Product\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * An order of several products.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Product\Order\Entry")
 * @ORM\Table(name="br_products_orders_entries")
 */
class Entry
{
    /**
     * @var integer A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Order The order to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="productorder", referencedColumnName="id")
     */
    private $order;

    /**
     * @var Product The product of which this is an entry in the order.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    private $product;

    /**
     * @var integer The quantity of this item.
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @param Order   $order    The order of which this entry is part.
     * @param Product $product  The product belonging to this entry.
     * @param integer $quantity The quantity of this product that was ordered
     */
    public function __construct(Order $order, Product $product, $quantity)
    {
        $this->setOrder($order);
        $this->product = $product;
        $this->setQuantity($quantity);
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
     * @param  Order|null $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param  integer $quantity
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
}
