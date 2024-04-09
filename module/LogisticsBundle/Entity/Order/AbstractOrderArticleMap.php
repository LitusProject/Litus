<?php

namespace LogisticsBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Order;

/**
 * The abstract class for a general order-article mapping
 * * Inheritors:
 * *  - OrderInventoryArticleMap
 * *  - OrderFlesserkeArticleMap
 * *
 * * @ORM\MappedSuperclass
 */
abstract class AbstractOrderArticleMap
{
    /**
     * @static
     * @var array All the possible states allowed
     */
    public static array $STATES = array(
        'requested' => 'requested',
        'approved'  => 'approved',
        'declined'  => 'declined',
        'reviewed'  => 'reviewed',
    );

    /**
     * @var Order The order of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(name="order", referencedColumnName="id", onDelete="CASCADE")
     */
    private Order $order;

    /**
     * @var integer The amount of this article in this order
     *
     * @ORM\Column(type="bigint")
     */
    private int $amount;

    /**
     * @var integer The amount of this article in the previous order
     *
     * @ORM\Column(type="bigint", options={"default" = 0})
     */
    private int $oldAmount;

    /**
     * @var string The status of this article in this order
     *
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getOldAmount(): int
    {
        return $this->oldAmount;
    }

    public function setOldAmount($amount): self
    {
        $this->oldAmount = $amount;

        return $this;
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
}
