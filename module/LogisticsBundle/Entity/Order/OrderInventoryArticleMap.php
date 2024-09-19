<?php

namespace LogisticsBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\InventoryArticle;

/**
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order\OrderInventoryArticleMap")
 * @ORM\Table(name="logistics_order_order_inventory_article_map")
 */
class OrderInventoryArticleMap extends AbstractOrderArticleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private int $id;

    /**
     * @var InventoryArticle The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\InventoryArticle")
     * @ORM\JoinColumn(name="article", referencedColumnName="id", onDelete="CASCADE")
     */
    private InventoryArticle $article;

    public function getArticle(): InventoryArticle
    {
        return $this->article;
    }

    public function setArticle(InventoryArticle $article): self
    {
        $this->article = $article;

        return $this;
    }
}
