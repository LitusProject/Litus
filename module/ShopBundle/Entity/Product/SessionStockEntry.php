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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM,
    ShopBundle\Entity\Product,
    ShopBundle\Entity\SalesSession;

/**
 * This entity stores how many products of a certain type will be available for sale during a certain sales session.
 *
 * @author Floris Kint <floris.kint@litus.cc>
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Product\SessionStockEntry")
 * @ORM\Table(name="shop.session_stock_entries")
 */
class SessionStockEntry
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
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\SalesSession")
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
     * @return int
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
