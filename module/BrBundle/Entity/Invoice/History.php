<?php

namespace BrBundle\Entity\Invoice;

use BrBundle\Entity\Invoice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\History")
 * @ORM\Table(name="br_invoices_history")
 */
class History
{
    /**
     * @var integer The ID of this article history
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Invoice The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var ArrayCollection The oldest version of the two
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Invoice\Entry", mappedBy="invoice", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $entries;

    /**
     * @var integer The version of the invoice this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;

        if ($invoice->hasContract()) {
            $this->entries = new ArrayCollection($invoice->getEntries());
        } else {
            $this->entries = new ArrayCollection();
        }

        $this->version = $invoice->getVersion();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}
