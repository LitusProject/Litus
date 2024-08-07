<?php

namespace BrBundle\Entity\Product;

use BrBundle\Entity\Collaborator;
use BrBundle\Entity\Contract;
use BrBundle\Entity\Product\Order\Entry;
use BrBundle\Entity\User\Person\Corporate as CorporatePerson;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * An order of several products.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Product\Order")
 * @ORM\Table(name="br_products_orders")
 */
class Order
{
    /**
     * @var integer The ID of this node
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
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Contract", mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private $contract;

    /**
     * @var \BrBundle\Entity\Invoice\Contract The invoice accompanying this order
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Invoice\Contract", mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private $invoice;

    /**
     * @var ArrayCollection The entries in this order
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Product\Order\Entry", mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private $entries;

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
     * @var boolean True if this order is old or not.
     *
     * @ORM\Column(name="old", type="boolean")
     */
    private $old;

    /**
     * @var boolean True if this order gets an automatic discount when the total price is high enough.
     *
     * @ORM\Column(name="auto_discount", type="boolean")
     */
    private $autoDiscount;

    /**
     * @var integer The discount percentage the company has on this order.
     *
     * @ORM\Column(name="auto_discount_percentage", type="integer", nullable = true)
     */
    private $autoDiscountPercentage;

    /**
     * @var integer The discount the company gets.
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
        $this->entries = new ArrayCollection();
        $this->old = false;
    }

    public function hasDiscount()
    {
        return $this->discount > 0;
    }

    /**
     * @param  integer $discount
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
     * @return integer discount in cents
     */
    public function getDiscount()
    {
        return $this->discount ?? 0;
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

        foreach ($this->entries as $entry) {
            if (!$entry->getProduct()->isRefund()) {
                $entry->getProduct()->setEntityManager($this->entityManager);
                $costNoRefund += $entry->getProduct()->getSignedPrice() * $entry->getQuantity();
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
     * @return integer
     */
    public function getAutoDiscountPercentage()
    {
        return $this->autoDiscountPercentage == null ? 0 : $this->autoDiscountPercentage;
    }

    /**
     * @return integer
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
        return $this->entries->toArray();
    }

    /**
     * @param  Entry $entry
     * @return self
     */
    public function setEntry(Entry $entry)
    {
        $this->entries->add($entry);

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
        return ($this->getContract() !== null);
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
     * @return \BrBundle\Entity\Invoice\Contract
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
            $result .= $entry->getProduct()->getName() . ': ' .
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
     * @param  integer $vatType
     * @return float combined cost of all entries with the given vat type, in cents
     */
    public function getCostVatTypeExclusive($vatType)
    {
        $cost = 0;

        foreach ($this->entries as $entry) {
            $entry->getProduct()->setEntityManager($this->entityManager);

            if ($entry->getProduct()->getVatPercentage() == $vatType) {
                $cost += (float) ($entry->getProduct()->getSignedPrice() * $entry->getQuantity());
            }
        }

        $cost = $cost - ($cost * $this->getAutoDiscountPercentage() / 100);

        return (float) $cost;
    }

    /**
     * @return float combined cost of all entries without VAT, in cents
     */
    public function getFullCostExclusive()
    {
        $cost = 0;

        foreach ($this->entries as $entry) {
            $entry->getProduct()->setEntityManager($this->entityManager);
            $cost += $entry->getProduct()->getSignedPrice() * $entry->getQuantity();
        }

        return (float) $cost;
    }

    /**
     * @return float cost of this order, with auto discount, in euro's
     */
    public function getTotalCostExclusive()
    {
        $cost = $this->getFullCostExclusive();

        $cost -= $this->getDiscount();

        $cost = $cost - ($cost * $this->getAutoDiscountPercentage() / 100);

        return (float) $cost / 100;
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
