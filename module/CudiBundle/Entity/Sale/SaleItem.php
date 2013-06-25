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

namespace CudiBundle\Entity\Sale;

use CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    CudiBundle\Entity\Sale\QueueItem,
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
     * @var \CudiBundle\Entity\Sale\Session The session of the sale item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var \CudiBundle\Entity\Sale\Article The article of the sale item
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
     * @var integer The price of the selling
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var \CudiBundle\Entity\Sale\QueueItem The queue item belonging to the sale item
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
     * @param \CudiBundle\Entity\Sale\Article $article
     * @param integer $number
     * @param integer $price
     * @param \CudiBundle\Entity\Sale\QueueItem|null $queueItem
     * @param string $discountType
     * @param \Doctrine\ORM\EntityManager|null $entityManager
     */
    public function __construct(Article $article, $number, $price, QueueItem $queueItem = null, $discountType = null, EntityManager $entityManager = null)
    {
        if (null == $queueItem) {
            if (null == $entityManager)
                throw new \InvalidArgumentException('EntityManager must be set');
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
     * @return \CudiBundle\Entity\Sale\Booking
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
     * @return \CudiBundle\Entity\Sale\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param integer $number
     *
     * @return \CudiBundle\Entity\Sale\SaleItem
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
     * @return \CudiBundle\Entity\Sale\QueueItem
     */
    public function getQueueItem()
    {
        return $this->queueItem;
    }

    /**
     * @return \CommonBundle\Entity\User\person
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
