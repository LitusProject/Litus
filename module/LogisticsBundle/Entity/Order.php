<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Order\OrderFlesserkeArticleMap;
use LogisticsBundle\Entity\Order\OrderInventoryArticleMap;

/**
 * This is the entity for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order")
 * @ORM\Table(name="logistics_order")
 */
class Order
{
    /**
     * @static
     * @var array All the possible states allowed
     */
    public static array $STATES = array(
        'Requested' => 'Requested',
        'Approved'  => 'Approved',
        'Declined'  => 'Declined',
        'Reviewed'  => 'Reviewed',
        'Canceled'  => 'Canceled',
        'Removed'   => 'Removed',
        'Reverted'  => 'Reverted',
    );

    /**
     * @static
     * @var array All the possible transportation modes
     */
    public static array $TRANSPORTS = array(
        'Self'        => 'Self',
        'Cargo bike'  => 'Cargo bike',
        'Car'         => 'Car',
        'Van'         => 'Van',
    );

    /**
     * @var integer The order's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @var OrderHistory The category of this article
     *
     * @ORM\ManyToOne(inversedBy="order", targetEntity="LogisticsBundle\Entity\OrderHistory", cascade={"persist"})
     * @ORM\JoinColumn(name="history", referencedColumnName="id")
     */
    private OrderHistory $history;

    /**
     * @var bool If this is the active order
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @var Academic The creator used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    private Academic $creator;

    /**
     * @var ArrayCollection The units associated with the order: gives access to the whole unit to view the order
     *
     * @ORM\ManyToMany(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinTable(
     *       name="order_unit",
     *       joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *       inverseJoinColumns={@ORM\JoinColumn(name="unit_id", referencedColumnName="id")}
     *  )
     */
    private Collection $units;

    /**
     * @var Academic The person who updated this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="updater", referencedColumnName="id")
     */
    private Academic $updater;

    /**
     * @var DateTime The date this article was last updated
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private DateTime $updateDate;

    /**
     * @var string The order's name
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $name;

    /**
     * @var string The description of the order
     *
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @var string The location of the order
     *
     * @ORM\Column(type="string")
     */
    private string $location;

    /**
     * @var DateTime The start date and time of this order.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private DateTime $startDate;

    /**
     * @var DateTime The end date and time of this order.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private DateTime $endDate;

    /**
     * @var ArrayCollection The inventory articles in this order
     *
     * @ORM\OneToMany(mappedBy="order", targetEntity="LogisticsBundle\Entity\Order\OrderInventoryArticleMap", orphanRemoval=true)
     * @ORM\JoinColumn(name="inventory_articles", referencedColumnName="id")
     */
    private Collection $inventoryArticles;

    /**
     * @var ArrayCollection The flesserke articles in this order
     *
     * @ORM\OneToMany(mappedBy="order", targetEntity="LogisticsBundle\Entity\Order\OrderFlesserkeArticleMap", orphanRemoval=true)
     * @ORM\JoinColumn(name="flesserke_articles", referencedColumnName="id")
     */
    private Collection $flesserkeArticles;

    /**
     * @var ArrayCollection The c&g articles in this order
     *
     * @ORM\OneToMany(mappedBy="order", targetEntity="LogisticsBundle\Entity\CGArticle", orphanRemoval=true)
     * @ORM\JoinColumn(name="cg_articles", referencedColumnName="id")
     */
    private Collection $cgArticles;

    /**
     * @var string The status of this order
     *
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    /**
     * @var array The type of transportation this order needs
     *
     * @ORM\Column(name="transport", type="array")
     */
    private array $transport;

    public function __construct(Academic $academic)
    {
        $this->active = true;
        $this->history = new OrderHistory();
        $this->creator = $academic;
        $this->updater = $academic;
        $this->units = new ArrayCollection();
        $this->inventoryArticles = new ArrayCollection();
        $this->flesserkeArticles = new ArrayCollection();
        $this->cgArticles = new ArrayCollection();
        $this->updateDate = new DateTime();
        $this->status = 'Requested';
        $this->transport = array();
    }

    public function update(Order $order, Academic $academic): Order
    {
        $this->active = true;
        $order->deactivate();

        $this->updater = $academic;
        $this->updateDate = new DateTime();
        $this->status = 'Requested';

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHistory(): OrderHistory
    {
        return $this->history;
    }

    public function setHistory(OrderHistory $history): self
    {
        $this->history = $history;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): self
    {
        $this->active = true;

        return $this;
    }

    public function deactivate(): self
    {
        $this->active = false;

        return $this;
    }

    public function getCreator(): Academic
    {
        return $this->creator;
    }

    public function setCreator(Academic $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function setUnits(Collection $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->removeElement($unit)) {
            $this->units->removeElement($unit);
        }

        return $this;
    }

    public function getUpdater(): Academic
    {
        return $this->updater;
    }

    public function setUpdater(Academic $updater): self
    {
        $this->updater = $updater;

        return $this;
    }

    public function getUpdateDate(): DateTime
    {
        return $this->updateDate;
    }

    /**
     * @return $this
     *
     * @ORM\PreUpdate
     */
    public function setUpdateDate(): self
    {
        $this->updateDate = new DateTime();

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getInventoryArticles(): Collection
    {
        return $this->inventoryArticles;
    }

    public function addInventoryArticle(OrderInventoryArticleMap $article): self
    {
        if (!$this->inventoryArticles->contains($article)) {
            $this->inventoryArticles->add($article);
        }

        return $this;
    }

    public function removeInventoryArticle(OrderInventoryArticleMap $article): self
    {
        if ($this->inventoryArticles->removeElement($article)) {
            $this->inventoryArticles->removeElement($article);
        }

        return $this;
    }

    public function getFlesserkeArticles(): Collection
    {
        return $this->flesserkeArticles;
    }

    public function addFlesserkeArticle(OrderInventoryArticleMap $article): self
    {
        if (!$this->inventoryArticles->contains($article)) {
            $this->inventoryArticles->add($article);
        }

        return $this;
    }

    public function removeFlesserkeArticle(OrderFlesserkeArticleMap $article): self
    {
        if ($this->flesserkeArticles->removeElement($article)) {
            $this->flesserkeArticles->removeElement($article);
        }

        return $this;
    }

    public function getCgArticles(): Collection
    {
        return $this->cgArticles;
    }

    public function addCGArticle(CGArticle $article): self
    {
        if (!$this->cgArticles->contains($article)) {
            $this->cgArticles->add($article);
        }

        return $this;
    }

    public function removeCGArticle(CGArticle $article): self
    {
        if ($this->cgArticles->removeElement($article)) {
            $this->cgArticles->removeElement($article);
        }

        return $this;
    }

    public function getAllArticles(): Collection
    {
        return new ArrayCollection(
            array_merge(
                $this->getInventoryArticles()->toArray(),
                $this->getFlesserkeArticles()->toArray(),
                $this->getCgArticles()->toArray(),
            )
        );
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTransport(): array
    {
        return $this->transport;
    }

    public function setTransport(array $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function addTransport(string $transport): self
    {
        if (!in_array($transport, $this->transport, true)) {
            $this->transport[] = $transport;
        }

        return $this;
    }

    public function removeTransport(string $transport): self
    {
        if (in_array($transport, $this->transport, true)) {
            unset($this->transport[array_search($transport, $this->transport, true)]);
        }

        return $this;
    }
}
