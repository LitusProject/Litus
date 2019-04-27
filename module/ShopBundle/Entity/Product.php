<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
     */
    private $id;

    /**
     * @var string The name of this product
     *
     * @ORM\Column(type="text")
     */
    private $name;

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
     * @return string
     */
    public function getName()
    {
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
}
