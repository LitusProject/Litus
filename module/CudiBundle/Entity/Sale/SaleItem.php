<?php

namespace CudiBundle\Entity\Sale;

use CudiBundle\Entity\Sale\Article\Discount\Discount;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\SaleItem")
 * @ORM\Table(
 *     name="cudi_sale_sale_items",
 *     indexes={@ORM\Index(name="cudi_sale_sale_items_timestamp", columns={"timestamp"})}
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "regular"="CudiBundle\Entity\Sale\SaleItem",
 *     "prof"="CudiBundle\Entity\Sale\SaleItem\Prof",
 *     "external"="CudiBundle\Entity\Sale\SaleItem\External"
 * })
 */
class SaleItem
{
    /**
     * @var integer The ID of the sale item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time the sale item was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var Session The session of the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var Article The article of the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer The number sold of the article
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var integer The price of the purchase
     *
     * @ORM\Column(name="purchase_price", type="integer")
     */
    private $purchasePrice;

    /**
     * @var integer The price of the selling
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var QueueItem The queue item belonging to the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\QueueItem", inversedBy="saleItems")
     * @ORM\JoinColumn(name="queue_item", referencedColumnName="id")
     */
    private $queueItem;

    /**
     * @var string The type of discount given
     *
     * @ORM\Column(name="discount_type", type="string", nullable=true)
     */
    private $discountType;

    /**
     * @param Article            $article
     * @param integer            $number
     * @param integer            $price
     * @param QueueItem|null     $queueItem
     * @param string|null        $discountType
     * @param EntityManager|null $entityManager
     */
    public function __construct(Article $article, $number, $price, QueueItem $queueItem = null, $discountType = null, EntityManager $entityManager = null)
    {
        if ($queueItem == null) {
            if ($entityManager == null) {
                throw new InvalidArgumentException('EntityManager must be set');
            }
            $this->session = $entityManager->getRepository('CudiBundle\Entity\Sale\Session')
                ->getLast();
        } else {
            $this->queueItem = $queueItem;
            $this->session = $queueItem->getSession();
        }

        $this->article = $article;
        $this->number = $number;
        $this->price = $price * 100;
        $this->timestamp = new DateTime();
        $this->discountType = $discountType;
        $this->purchasePrice = (int) ($article->getPurchasePrice() * $number);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param DateTime $timestamp
     *
     * @return SaleItem
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param integer $number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->purchasePrice = (int) round($this->purchasePrice * $number / $this->number);
        $this->price = (int) round($this->price * $number / $this->number);
        $this->number = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param  integer $price
     * @return self
     */
    public function setPurchasePrice($price)
    {
        $this->purchasePrice = $price;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return QueueItem
     */
    public function getQueueItem()
    {
        return $this->queueItem;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson()
    {
        return $this->queueItem->getPerson();
    }

    /**
     * @return string|null
     */
    public function getDiscountType()
    {
        if (isset(Discount::$possibleTypes[$this->discountType])) {
            return Discount::$possibleTypes[$this->discountType];
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'regular';
    }
}
