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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
     * @var integer The version of the invoice this entry belongs to
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var ArrayCollection The oldest version of the two
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Invoice\Entry", mappedBy="invoice", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $entries;

    /**
     * @var string The name of the file
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $file;

    /**
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->version = $invoice->getVersion();

        $entries = array();
        if ($invoice->hasContract()) {
            $entries = $invoice->getEntries();
        }

        $this->entries = new ArrayCollection($entries);

        if ($invoice instanceof Manual) {
            $this->file = $invoice->getFile();
        }
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
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
