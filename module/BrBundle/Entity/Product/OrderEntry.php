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

namespace BrBundle\Entity\Product;

use BrBundle\Entity\Product,
    BrBundle\Entity\Product\Order,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * An order of several products.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Product\OrderEntry")
 * @ORM\Table(name="br.orders_entries")
 */
class OrderEntry
{

    /**
     * @var int A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Product\Order The order to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var \BrBundle\Entity\Product The product of which this is an entry in the order.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    private $product;

    /**
     * @var \BrBundle\Entity\Contract\ContractEntry The contract entry accompanying this order entry
     *
     * @ORM\OneToOne(
     *      targetEntity="BrBundle\Entity\Contract\ContractEntry",
     *      mappedBy="orderEntry",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $contractEntry;

    /**
     * @var int The quantity of this item.
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @param \BrBundle\Entity\Product\Order $order The order of which this entry is part.
     * @param \BrBundle\Entity\Product $product The product belonging to this entry.
     * @param int $quantity The quantity of this product that was ordered
     */
    public function __construct(Order $order, Product $product, $quantity)
    {
        $this->order = $order;
        $this->product = $product;
        $this->setQuantity($quantity);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Product\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \BrBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \BrBundle\Entity\Contract\ContractEntry
     */
    public function getContractEntry()
    {
        return $this->contractEntry;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return \BrBundle\Entity\Product\OrderEntry
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }
}
