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

namespace BrBundle\Entity;

use BrBundle\Entity\Company,
    BrBundle\Entity\Product,
    BrBundle\Entity\Product\Order,
    CommonBundle\Entity\User\Person,
    DateInterval,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a invoice.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice")
 * @ORM\Table(name="br.invoices")
 */
class Invoice
{
    /**
     * @var int The invoice's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Product\Order The order for which this invoice is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var \DateTime The time of creation of this invoice
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \DateTime The time this invoice was paid.
     *
     * @ORM\Column(name="paid_time", type="datetime", nullable=true)
     */
    private $paidTime;

    /**
     * @var \BrBundle\Entity\Invoice\InvoiceEntry The entries in this invoice
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Invoice\InvoiceEntry",
     *      mappedBy="invoice",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $invoiceEntries;

    /**
     * @var Integer that resembles the version of this invoice.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * Creates a new invoice
     *
     * @param \BrBundle\Entity\Product\Order $order The order to create the invoice for.
     */
    public function __construct(Order $order)
    {
        $this->setOrder($order);
        $this->creationTime = new DateTime();
        $this->setVersion(0);
    }

    public function getInvoiceNumber()
    {
        return $this->creationTime->format('Y').str_pad($this->order->getContract()->getInvoiceNb(), 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param  int                      $version
     * @return \BrBundle\Entity\Invoice
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isPayed()
    {
        if ($this->paidTime == null)
            return false;
        return true;
    }

    /**
     * @return \BrBundle\Entity\Invoice
     * @throws InvalidArgumentExeption
     *                                  Exception gets thrown if the invoice is already payed.
     */
    public function setPayed()
    {
        if ($this->isPayed())
            throw new \InvalidArgumentException('This invoice has already been paid');
        $this->paidTime = new DateTime();

        return $this;
    }

    /**
     * @return BrBundle\Entity\Product\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  \BrBundle\Entity\Product\Order $order
     * @return \BrBundle\Entity\Invoice
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

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
     * @return bool
     */
    public function isExpired(EntityManager $entityManager)
    {
        $now = new DateTime();

        return !$this->isPaid() && $now > $this->getExpirationTime($entityManager);
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime(EntityManager $entityManager)
    {
        $expireTime = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_expire_time');

        return $this->getCreationTime()->add(new DateInterval($expireTime));
    }

    /**
     * @return \DateTime
     */
    public function getPaidTime()
    {
        return $this->paidTime;
    }

    /**
     * @return \DateTime
     */
    public function setPaidTime($paidTime)
    {
        if ($this->isPaid())
            throw new \InvalidArgumentException('This invoice has already been paid');

        $this->paidTime = $paidTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return NULL !== $this->paidTime;
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
            if($entry->getVersion() == $version)
                array_push($array, $entry);
        }

        return $array;
    }

}
