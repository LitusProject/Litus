<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Entity\Sales;

use CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Sales\Articles\Discounts\Discount,
    CudiBundle\Entity\Sales\QueueItem,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\SaleItem")
 * @ORM\Table(name="cudi.sales_sale_items", indexes={@ORM\Index(name="sales_sale_item_time", columns={"timestamp"})})
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
     * @var \DateTime The time the sale item was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CudiBundle\Entity\Sales\Session The session of the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
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
     * @var integer The price of the selling
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var \CudiBundle\Entity\Sales\QueueItem The queue item belonging to the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\QueueItem", inversedBy="saleItems")
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
     * @param \CudiBundle\Entity\Sales\Article $article
     * @param integer $number
     * @param integer $price
     * @param \CudiBundle\Entity\Sales\QueueItem|null $queueItem
     * @param string $discountType
     * @param \Doctrine\ORM\EntityManager|null $entityManager
     */
    public function __construct(Article $article, $number, $price, QueueItem $queueItem = null, $discountType = null, EntityManager $entityManager = null)
    {
        if (null == $queueItem) {
            if (null == $entityManager)
                throw new \InvalidArgumentException('EntityManager must be set');
            $this->session = $entityManager->getRepository('CudiBundle\Entity\Sales\Session')
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
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $timestamp
     *
     * @return \CudiBundle\Entity\Sales\Booking
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param integer $number
     *
     * @return \CudiBundle\Entity\Sales\SaleItem
     */
    public function setNumber($number)
    {
        $this->price = round($this->price * $number / $this->number);
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
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return \CudiBundle\Entity\Sales\QueueItem
     */
    public function getQueueItem()
    {
        return $this->queueItem;
    }

    /**
     * @return \CommonBundle\Entity\Users\person
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
        if (isset(Discount::$POSSIBLE_TYPES[$this->discountType]))
            return Discount::$POSSIBLE_TYPES[$this->discountType];
    }
}
