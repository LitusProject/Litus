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
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\InvoiceHistory")
 * @ORM\Table(name="br.invoice_history")
 */
class InvoiceHistory
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
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Invoice\InvoiceEntry", mappedBy="invoice", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $entries;

    /**
     * @var int The version of the invoice this entry belongs too.
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
        $this->entries = new ArrayCollection($invoice->getEntries());
        $this->version = $invoice->getVersion();
    }

    /**
     * @return int
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
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
