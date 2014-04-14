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

namespace BrBundle\Entity\Invoice;

use BrBundle\Entity\Invoice,
    BrBundle\Entity\Product\OrderEntry,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * An entry of an invoice.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\InvoiceEntry")
 * @ORM\Table(name="br.invoices_entries")
 */
class InvoiceEntry
{

    /**
     * @var int A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Invoice The invoice to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var string The contract text of this product
     *
     * @ORM\Column(name="contract_text", type="text")
     */
    private $invoiceText;

    /**
     * @var \BrBundle\Entity\Product\OrderEntry The order entry of which this is an entry in the invoice.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\OrderEntry")
     * @ORM\JoinColumn(name="order_entry", referencedColumnName="id")
     */
    private $orderEntry;

    /**
     * @var int The position number of the entry in the invoice
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var int The version of the contract this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param \BrBundle\Entity\Invoice $invoice The invoice of which this entry is part.
     * @param \BrBundle\Entity\Product\OrderEntry $orderEntry The order entry corresponding to this invoice entry.
     * @param int $position The position number of the entry in the invoice
     */
    public function __construct(Invoice $invoice, OrderEntry $orderEntry, $position, $version)
    {
        $this->invoice = $invoice;
        $this->orderEntry = $orderEntry;
        $this->position = $position;
        $this->setInvoiceText($orderEntry->getProduct()->getInvoiceDescription());
        $this->_setVersion($version);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $versionNmbr
     */
    private function _setVersion($versionNmbr)
    {
        if($versionNmbr < 0)
            throw new \InvalidArgumentException("version number must be larger or equal to zero");
        $this->version = $versionNmbr;
    }

    /**
     * @param int $text
     * @return \BrBundle\Entity\Invoice\InvoiceEntry
     */
    public function setInvoiceText($text)
    {
        $this->invoiceText = $text;

        return $this;
    }

    /**
     * @return text
     */
    public function getInvoiceText()
    {
        return $this->invoiceText;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function getInvoiceDescription()
    {
        return $this->getOrderEntry()->getProduct()->getInvoiceDescription();
    }

    /**
     * @return \BrBundle\Entity\Product\OrderEntry
     */
    public function getOrderEntry()
    {
        return $this->orderEntry;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
