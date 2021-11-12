<?php

namespace ShopBundle\Entity\Session;

use Doctrine\ORM\Mapping as ORM;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\Session as SalesSession;

/**
 * This entity stores how many products of a certain type will be available for sale during a certain sales session.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Session\Stock")
 * @ORM\Table(name="shop_sessions_stock")
 */
class Stock
{
    /**
     * @var Product The product of this session stock entry.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    private $product;

    /**
     * @var SalesSession The id of the sales session
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id", onDelete="cascade")
     */
    private $salesSession;

    /**
     * @var integer The amount of products available during this sales session.
     *
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $product
     * @return self
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return SalesSession
     */
    public function getSalesSession()
    {
        return $this->salesSession;
    }

    /**
     * @param  SalesSession $salesSession
     * @return self
     */
    public function setSalesSession($salesSession)
    {
        $this->salesSession = $salesSession;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
