<?php

namespace CudiBundle\Entity\Sale;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\ReturnItem")
 * @ORM\Table(
 *     name="cudi_sale_return_items",
 *     indexes={@ORM\Index(name="cudi_sale_return_items_timestamp", columns={"timestamp"})}
 * )
 */
class ReturnItem
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
     * @param Article            $article
     * @param integer            $price
     * @param QueueItem|null     $queueItem
     * @param EntityManager|null $entityManager
     */
    public function __construct(Article $article, $price, QueueItem $queueItem = null, EntityManager $entityManager = null)
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
        $this->price = $price * 100;
        $this->timestamp = new DateTime();
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
     * @return self
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
}
