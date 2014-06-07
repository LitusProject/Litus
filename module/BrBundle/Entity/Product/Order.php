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

use BrBundle\Entity\Company,
    BrBundle\Entity\Collaborator,
    BrBundle\Entity\User\Person\Corporate as CorporatePerson,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

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
     * @var \BrBundle\Entity\User\Person\Corporate The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var \BrBundle\Entity\Contract The contract accompanying this order
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
     * @var \BrBundle\Entity\Invoice The invoice accompanying this order
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
     * @var \BrBundle\Entity\Product\OrderEntry The entries in this order
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
     * @var \DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \CommonBundle\Entity\User\Person The person who created this node
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
     * @var bool True if this order is tax free, false if not.
     *
     * @ORM\Column(name="tax_free", type="boolean")
     */
    private $taxFree;

    /**
     * @var int that determines the maximum cost that can be given to an order.
     **/
    private static $MAX_TOTAL_COST = 50000;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param \BrBundle\Entity\User\Person\Corporate $contact
     * @param \BrBundle\Entity\Collaborator          $creationPerson
     * @param boolean                                $taxFree
     */
    public function __construct(CorporatePerson $contact, Collaborator $creationPerson, $taxFree)
    {
        $this->setContact($contact);
        $this->creationTime = new DateTime();
        $this->creationPerson = $creationPerson;
        $this->orderEntries = new ArrayCollection();
        $this->old = false;
        $this->setTaxFree($taxFree);
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
     * @return \BrBundle\Entity\User\Person\Corporate
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @ return \BrBundle\Entity\Product\Order
     */
    public function setContact(CorporatePerson $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
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

    public function setEntry(OrderEntry $entry)
    {
        $this->orderEntries->add($entry);

        return $this;
    }

    /**
     * @return \BrBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
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
     * @note    This order gets set to old.
     *          This means the boolean $old is set to true.
     */
    public function setOld()
    {
        $this->old = true;
    }

    /**
     * @return boolean
     */
    public function isTaxFree()
    {
        return $this->taxFree;
    }

    /**
     * @param boolean $taxfree
     */
    public function setTaxFree($taxFree)
    {
        $this->taxFree = $taxFree;
    }

    /**
     * @return double        cost of this order
     */
    public function getTotalCost()
    {
        $cost = 0;
        if ($this->taxFree) {
            foreach ($this->orderEntries as $orderEntry)
                $cost = $cost + ($orderEntry->getProduct()->getPrice() * $orderEntry->getQuantity());
        } else {
            foreach ($this->orderEntries as $orderEntry) {
                $orderEntry->getProduct()->setEntityManager($this->_entityManager);
                $cost = $cost + (($orderEntry->getProduct()->getPrice() * (1 + $orderEntry->getProduct()->getVatPercentage()/100)) * $orderEntry->getQuantity()) ;
            }
        }

        return ($cost / 100) - $this->getContract()->getDiscount();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }
}
