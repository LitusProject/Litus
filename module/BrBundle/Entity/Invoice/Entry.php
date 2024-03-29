<?php

namespace BrBundle\Entity\Invoice;

use BrBundle\Entity\Invoice;
use BrBundle\Entity\Product\Order\Entry as OrderEntry;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * An entry of an invoice.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\Entry")
 * @ORM\Table(name="br_invoices_entries")
 */
class Entry
{
    /**
     * @var integer A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Invoice The invoice to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var string The contract text of this product
     *
     * @ORM\Column(name="invoice_text", type="text")
     */
    private $invoiceText;

    /**
     * @var OrderEntry The order entry of which this is an entry in the invoice.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order\Entry")
     * @ORM\JoinColumn(name="order_entry", referencedColumnName="id")
     */
    private $orderEntry;

    /**
     * @var integer The position number of the entry in the invoice
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var integer The version of the contract this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Invoice    $invoice    The invoice of which this entry is part.
     * @param OrderEntry $orderEntry The order entry corresponding to this invoice entry.
     * @param integer    $position   The position number of the entry in the invoice
     * @param integer    $version    The version of the contract this entry belongs too
     */
    public function __construct(Invoice $invoice, OrderEntry $orderEntry, $position, $version)
    {
        $this->invoice = $invoice;
        $this->orderEntry = $orderEntry;
        $this->position = $position;
        $this->setInvoiceText($orderEntry->getProduct()->getInvoiceDescription());
        $this->setVersion($version);
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param integer $versionNmbr
     */
    private function setVersion($versionNmbr)
    {
        if ($versionNmbr < 0) {
            throw new InvalidArgumentException('version number must be larger or equal to zero');
        }

        $this->version = $versionNmbr;
    }

    /**
     * @param  string $text
     * @return self
     */
    public function setInvoiceText($text)
    {
        $this->invoiceText = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceText()
    {
        return $this->invoiceText;
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
     * @return string
     */
    public function getInvoiceDescription()
    {
        return $this->getOrderEntry()->getProduct()->getInvoiceDescription();
    }

    /**
     * @return OrderEntry
     */
    public function getOrderEntry()
    {
        return $this->orderEntry;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
