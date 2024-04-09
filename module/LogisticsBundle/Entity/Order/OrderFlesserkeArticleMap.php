<?php

namespace LogisticsBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\FlesserkeArticle;

/**
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order\OrderFlesserkeArticleMap")
 * @ORM\Table(name="logistics_order_order_flesserke_article_map")
 */
class OrderFlesserkeArticleMap extends AbstractOrderArticleMap
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
     * @var FlesserkeArticle The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\FlesserkeArticle")
     * @ORM\JoinColumn(name="article", referencedColumnName="id", onDelete="CASCADE")
     */
    private FlesserkeArticle $article;

    public function getArticle(): FlesserkeArticle
    {
        return $this->article;
    }

    public function setArticle(FlesserkeArticle $article): self
    {
        $this->article = $article;

        return $this;
    }
}
