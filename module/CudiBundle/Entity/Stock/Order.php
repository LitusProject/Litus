<?php

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Supplier;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Order")
 * @ORM\Table(
 *     name="cudi_stock_orders",
 *     indexes={@ORM\Index(name="cudi_stock_orders_date_created", columns={"date_created"})}
 * )
 */
class Order
{
    /**
     * @var integer The ID of the order
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Supplier The supplier of the order
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var DateTime The time the order was created
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var DateTime The time the order was ordered
     *
     * @ORM\Column(name="date_ordered", type="datetime", nullable=true)
     */
    private $dateOrdered;

    /**
     * @var DateTime The time the delivery is expected
     *
     * @ORM\Column(name="date_delivery", type="datetime", nullable=true)
     */
    private $dateDelivery;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The items ordered
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Stock\Order\Item", mappedBy="order")
     */
    private $items;

    /**
     * @var Person The person who ordered the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDelivered;

    /**
     * @param Supplier $supplier The supplier of this order
     */
    public function __construct(Supplier $supplier, Person $person)
    {
        $this->supplier = $supplier;
        $this->person = $person;
        $this->dateCreated = new DateTime();
    }

    /**
     * Get the id of this delivery
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Get the number of this order
     *
     * @return integer
     */
    public function getTotalNumber()
    {
        $number = 0;
        foreach ($this->items as $item) {
            $number += $item->getNumber();
        }

        return $number;
    }

    /**
     * Get the price of this order
     *
     * @return integer
     */
    public function getPrice()
    {
        $price = 0;
        foreach ($this->items as $item) {
            $price += $item->getPrice();
        }

        return $price;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return DateTime
     */
    public function getDateOrdered()
    {
        return $this->dateOrdered;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     *
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isOrdered()
    {
        return $this->dateOrdered !== null;
    }

    /**
     * @return self
     */
    public function setOrdered()
    {
        $this->dateOrdered = new DateTime();

        return $this;
    }

    /**
     * @return self
     */
    public function setCanceled()
    {
        $this->dateOrdered = null;

        return $this;
    }

    /**
     * @param DateTime $deliveryDate
     *
     * @return self
     */
    public function setDeliveryDate(DateTime $deliveryDate)
    {
        $deliveryDate->setTime(0, 0);
        $this->dateDelivery = $deliveryDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->dateDelivery;
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
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDelivered()
    {
        return $this->isDelivered;
    }

    /**
     * @param boolean $isDelivered
     *
     * @return $this
     */
    public function setDelivered($isDelivered)
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }
}
