<?php

namespace BrBundle\Entity\Invoice;

use BrBundle\Entity\Product\Order;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\Contract")
 * @ORM\Table(name="br_invoices_contracts")
 */
class Contract extends \BrBundle\Entity\Invoice
{
    /**
     * @var Order The order for which this invoice is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="product_order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var ArrayCollection The entries in this invoice
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Invoice\Entry", mappedBy="invoice", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $invoiceEntries;

    public function __construct(Order $order, EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->invoiceEntries = new ArrayCollection();

        $this->setOrder($order);
    }

    public function hasContract()
    {
        return true;
    }

    public function getCompany()
    {
        return $this->getOrder()->getCompany();
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        $expireTime = 'P' . $this->getOrder()->getContract()->getPaymentDays() . 'D';

        return $this->getCreationTime()->add(new DateInterval($expireTime));
    }

    public function getAuthor()
    {
        return $this->getOrder()->getContract()->getAuthor();
    }

    public function getTitle()
    {
        return $this->getOrder()->getContract()->getTitle();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllEntries()
    {
        return $this->invoiceEntries->toArray();
    }

    /**
     * @return array
     * @note    Only the most recent entries get returned in the array.
     */
    public function getEntries()
    {
        $version = $this->getVersion();

        $array = array();

        foreach ($this->getAllEntries() as $entry) {
            if ($entry->getVersion() == $version) {
                array_push($array, $entry);
            }
        }

        return $array;
    }
}
