<?php

namespace LogisticsBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Order;

/**
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order\OrderArticleMap")
 * @ORM\Table(name="logistics_order_order_article_map")
 */
class OrderArticleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Order The Order of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(name="referenced_order", referencedColumnName="id", onDelete="CASCADE")
     */
    private $referencedOrder;

    /**
     * @var Article The Article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Article")
     * @ORM\JoinColumn(name="referenced_article", referencedColumnName="id", onDelete="CASCADE")
     */
    private $referencedArticle;

    /**
     * @var integer amount of this Article in this order
     *
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @var integer amount of this Article in the previous order
     *
     * @ORM\Column(type="bigint", options={"default" = 0})
     */
    private $oldAmount;

    /**
     * @var string status of this Article-request in this order
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @var string comment about this Article-request in this order, used when review is done
     *
     * @ORM\Column(name="comment", type="text", options={"default" = ""}, nullable=true)
     */
    private $comment;

    /**
     * @static
     * @var array All the possible statuses allowed
     */
    public static $POSSIBLE_STATUSES = array(
        'aangevraagd' => 'Aangevraagd',
        'goedgekeurd' => 'Goedgekeurd',
        'afgewezen'   => 'Afgewezen',
        'herzien'     => 'Herzien',
        'op locatie'  => 'Op Locatie',
        'terug'       => 'Terug',
        'klaar'       => 'Klaar',
        'weggezet'    => 'Weggezet',
        'none'        => 'None',
        'vuil'          => 'Vuil',
        'kapot'         => 'Kapot',
        'vermist'       => 'Vermist',
        'weg'           => 'Weg',
        'aankopen'      => 'Aankopen',
        'nakijken'      => 'Nakijken',
    );

    /**
     * OrderArticleMap constructor.
     * @param Order   $order
     * @param Article $article
     * @param integer $amount
     * @param integer $oldAmount
     * @param string  $comment
     */
    public function __construct(Order $order, Article $article, $amount, $oldAmount=0, $comment='')
    {
        $this->referencedOrder = $order;
        $this->referencedArticle = $article;
        $this->amount = $amount;
        $this->oldAmount = $oldAmount;
        $this->comment = $comment;

        if ($article->getStatusKey() === 'ok') {
            $this->status = 'aangevraagd';
        } else {
            $this->status = $article->getStatusKey();
        }
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->referencedOrder;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->referencedArticle;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return integer
     */
    public function getOldAmount()
    {
        return $this->oldAmount;
    }

    /**
     * @param integer $amount
     */
    public function setOldAmount($amount)
    {
        $this->oldAmount = $amount;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return OrderArticleMap::$POSSIBLE_STATUSES[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusKey()
    {
        return $this->status ? $this->status : 'none';
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->getOrder()->isApproved();
    }

    /**
     * @return boolean
     */
    public function isRejected()
    {
        return $this->getOrder()->isRejected();
    }

    /**
     * @return boolean
     */
    public function isReviewed()
    {
        return $this->getOrder()->isReviewed();
    }
}
