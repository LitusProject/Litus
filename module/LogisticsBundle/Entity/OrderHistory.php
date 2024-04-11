<?php

namespace LogisticsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Order;

/**
 * This entity stores the history for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\OrderHistory")
 * @ORM\Table(name="logistics_order_history")
 */
class OrderHistory
{
    /**
     * @var integer History's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @var ArrayCollection The orders in this category
     *
     * @ORM\OneToMany(mappedBy="history", targetEntity="LogisticsBundle\Entity\Order", orphanRemoval=true)
     * @ORM\JoinColumn(name="orders", referencedColumnName="id")
     */
    private Collection $orders;

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getLastOrder(): Order
    {
        return $this->orders->last();
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $this->orders->removeElement($order);
        }

        return $this;
    }
}
