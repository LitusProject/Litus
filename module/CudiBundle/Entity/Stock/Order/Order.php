<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Stock\Order;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Supplier,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Order\Order")
 * @ORM\Table(name="cudi.stock_orders", indexes={@ORM\Index(name="stock_orders_time", columns={"date_created"})})
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
     * @var \CudiBundle\Entity\Supplier The supplier of the order
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var \DateTime The time the order was created
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime The time the order was ordered
     *
     * @ORM\Column(name="date_ordered", type="datetime", nullable=true)
     */
    private $dateOrdered;

    /**
     * @var \DateTime The time the delivery is expected
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
     * @var \CommonBundle\Entity\User\Person The person who ordered the order
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
     * @param \CudiBundle\Entity\Supplier $supplier The supplier of this order
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
     * @return \CudiBundle\Entity\Supplier
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
        foreach($this->items as $item)
            $number += $item->getNumber();

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
        foreach($this->items as $item)
            $price += $item->getPrice();

        return $price;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateOrdered()
    {
        return $this->dateOrdered;
    }

    /**
     * @return \Doctrine\Common\Collection\ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param \CommonBundle\Entity\User\Person $person
     *
     * @return \CudiBundle\Entity\Stock\Order\Order
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
        return null !== $this->dateOrdered;
    }

    /**
     * @return \CudiBundle\Entity\Stock\Order\Order
     */
    public function order()
    {
        $this->dateOrdered = new DateTime();

        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Stock\Order\Order
     */
    public function cancel()
    {
        $this->dateOrdered = null;

        return $this;
    }

    /**
     * @param \DateTime $deliveryDate
     *
     * @return \CudiBundle\Entity\Stock\Order\Order
     */
    public function setDeliveryDate(DateTime $deliveryDate)
    {
        $deliveryDate->setTime(0, 0);
        $this->dateDelivery = $deliveryDate;

        return $this;
    }

    /**
     * @return \DateTime
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
     * @return \CudiBundle\Entity\Stock\Order\Order
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
