<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a product.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Product")
 * @ORM\Table(name="shop_products")
 */
class Product
{
    /**
     * @var integer The ID of this product
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     */
    private $id;

    /**
     * @var string The name of this product
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string The English name of this product
     * @ORM\Column(type="text", nullable=true)
     */
    private $name_en;

    /**
     * @var float The selling price of the product
     *
     * @ORM\Column(name="sell_price", type="decimal", precision=5, scale=2)
     */
    private $sellPrice;

    /**
     * @var boolean Whether this product is available
     *
     * @ORM\Column(type="boolean")
     */
    private $available;

    /**
     * @var integer Standard amount for product
     *
     * @ORM\Column(name="default_amount", type="integer",  nullable=true)
     */
    private $defaultAmount;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param  string $name_en
     * @return self
     */
    public function setNameEN($name_en)
    {
        $this->name_en = $name_en;
        return $this;
    }

    /**
     * @param string $lang
     * @return string
     */
    public function getName($lang = 'nl')
    {

        if ($lang === 'en') {
            if ($this->name_en !== null) {
                return $this->name_en;
            }
        }
        return $this->name;
    }

    /**
     * @param  float $sellPrice
     * @return self
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getSellPrice()
    {
        return floatval($this->sellPrice);
    }

    /**
     * @param  boolean $available
     * @return self
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @return integer
     */
    public function getDefaultAmount()
    {
        return $this->defaultAmount;
    }

    /**
     * @param integer $amount
     * @return self
     */
    public function setDefaultAmount($amount)
    {
        $this->defaultAmount = $amount;
        return $this;
    }
}
