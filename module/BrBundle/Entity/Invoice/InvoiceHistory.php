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
     * @var BrBundle\Entity\Invoice The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var BrBundle\Entity\Invoice\InvoiceEntry The oldest version of the two
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
     * @param \BrBundle\Entity\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->_setInvoice($invoice);
        $this->_setEntries($invoice);
        $this->version = $invoice->getVersion();
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    private function _setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    private function _setEntries(Invoice $invoice)
    {
        $this->entries = $invoice->getEntries();
    }

    public function getVersion()
    {
        return $this->version;
    }

}
