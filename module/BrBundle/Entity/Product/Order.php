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

namespace BrBundle\Entity\Product;

use BrBundle\Entity\Collaborator,
    BrBundle\Entity\Company,
    BrBundle\Entity\Contract,
    BrBundle\Entity\Invoice,
    BrBundle\Entity\User\Person\Corporate as CorporatePerson,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * An order of several products.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Product\Order")
 * @ORM\Table(name="br.orders")
 */
class Order
{
    /**
     * @var int The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CorporatePerson The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var Contract The contract accompanying this order
     *
     * @ORM\OneToOne(
     *      targetEntity="BrBundle\Entity\Contract",
     *      mappedBy="order",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $contract;

    /**
     * @var Invoice The invoice accompanying this order
     *
     * @ORM\OneToOne(
     *      targetEntity="BrBundle\Entity\Invoice",
     *      mappedBy="order",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $invoice;

    /**
     * @var ArrayCollection The entries in this order
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Product\OrderEntry",
     *      mappedBy="order",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $orderEntries;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var Collaborator The person who created this node
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Collaborator")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @var bool True if this order is old or not.
     *
     * @ORM\Column(name="old", type="boolean")
     */
    private $old;

    /**
     * @var bool True if this order gets an automatic discount when the total price is high enough.
     *
     * @ORM\Column(name="auto_discount", type="boolean")
     */
    private $autoDiscount;

    /**
     * @var int The discount percentage the company has on this order.
     *
     * @ORM\Column(name="auto_discount_percentage", type="integer", nullable = true)
     */
    private $autoDiscountPercentage;

    /**
     * @var int The discount the company gets.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discount;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param Collaborator $creationPerson
     */
    public function __construct(Collaborator $creationPerson)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $creationPerson;
        $this->orderEntries = new ArrayCollection();
        $this->old = false;
    }

    public function hasDiscount()
    {
        return $this->discount > 0;
    }

    /**
     * @param  int  $discount
     * @return self
     */
    public function setDiscount($discount)
    {
        if ($discount < 0) {
            throw new InvalidArgumentException('Invalid discount');
        }

        $this->discount = $discount;

        return $this;
    }

    /**
     * @return int discount in cents
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return self
     */
    public function setAutoDiscountPercentage()
    {
        $percentage = 0;

        $discounts = unserialize(
            $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.automatic_discounts')
        );

        $costNoRefund = 0;

        foreach ($this->orderEntries as $orderEntry) {
            if (!$orderEntry->getProduct()->isRefund()) {
                $orderEntry->getProduct()->setEntityManager($this->entityManager);
                $costNoRefund = $costNoRefund + ($orderEntry->getProduct()->getPrice() * $orderEntry->getQuantity());
            }
        }

        if ($this->hasAutoDiscount()) {
            foreach ($discounts as $price => $discount) {
                if ($costNoRefund >= $price && $discount > $percentage) {
                    $percentage = $discount;
                }
            }
        }

        $this->autoDiscountPercentage = $percentage;

        return $this;
    }

    /**
     * @return int
     */
    public function getAutoDiscountPercentage()
    {
        return $this->autoDiscountPercentage == null ? 0 : $this->autoDiscountPercentage;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->contact->getCompany();
    }

    /**
     * @return CorporatePerson
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return self
     */
    public function setContact(CorporatePerson $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return Collaborator
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->orderEntries->toArray();
    }

    /**
     * @param  OrderEntry $entry
     * @return self
     */
    public function setEntry(OrderEntry $entry)
    {
        $this->orderEntries->add($entry);

        return $this;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return boolean
     */
    public function hasContract()
    {
        return (null !== $this->getContract() ? true : false);
    }

    /**
     * @param  Contract $contract
     * @return self
     */
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return \BrBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $result = '';
        foreach ($this->getEntries() as $entry) {
            $result = $result .
                $entry->getProduct()->getName() . ': ' .
                $entry->getQuantity() . ', ';
        }

        return rtrim($result, ' ,');
    }

    /**
     * @return boolean
     */
    public function isOld()
    {
        return $this->old;
    }

    /**
     * @note   This order gets set to old.
     *              This means the boolean $old is set to true.
     */
    public function setOld()
    {
        $this->old = true;
    }

    /**
     * @return boolean
     */
    public function hasAutoDiscount()
    {
        return $this->autoDiscount;
    }

    /**
     * @param  boolean $autoDiscount
     * @return self
     */
    public function setAutoDiscount($autoDiscount)
    {
        $this->autoDiscount = $autoDiscount;

        return $this;
    }

    /**
     * @param  int    $vatType
     * @return double combined cost of all entries with the given vat type, in cents
     */
    public function getCostVatTypeExclusive($vatType)
    {
        $cost = 0;

        foreach ($this->orderEntries as $orderEntry) {
            $orderEntry->getProduct()->setEntityManager($this->entityManager);

            if ($orderEntry->getProduct()->getVatPercentage() == $vatType) {
                $cost = $cost + (double) ($orderEntry->getProduct()->getPrice() * $orderEntry->getQuantity());
            }
        }

        return (double) $cost;
    }

    /**
     * @return double combined cost of all entries without VAT, in cents
     */
    public function getFullCostExclusive()
    {
        $cost = 0;

        foreach ($this->orderEntries as $orderEntry) {
            $orderEntry->getProduct()->setEntityManager($this->entityManager);
            $cost = $cost + ($orderEntry->getProduct()->getPrice() * $orderEntry->getQuantity());
        }

        return (double) $cost;
    }

    /**
     * @return double cost of this order, with auto discount, in euro's
     */
    public function getTotalCostExclusive()
    {
        $cost = $this->getFullCostExclusive();

        $cost = $cost - ($this->getDiscount());

        $cost = ($cost - ($cost*$this->getAutoDiscountPercentage() / 100));

        return (double) $cost / 100;
    }

    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
