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

namespace BrBundle\Entity;

use BrBundle\Entity\Product,
    DateInterval,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * This is the entity for a invoice.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice")
 * @ORM\Table(name="br.invoices")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "manual"="BrBundle\Entity\Invoice\ManualInvoice",
 *      "contract"="BrBundle\Entity\Invoice\ContractInvoice"
 * })
 */
abstract class Invoice
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
     * @var bool True if this invoice is tax free.
     *
     * @ORM\Column(name="tax_free", type="boolean", options={"default" = false})
     */
    private $taxFree;

    /**
     * @var DateTime The time of creation of this invoice
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var DateTime The time this invoice was paid.
     *
     * @ORM\Column(name="paid_time", type="datetime", nullable=true)
     */
    private $paidTime;

    /**
     * @var int The invoice number;
     *
     * @ORM\Column(name="invoice_nb", type="integer")
     */
    private $invoiceNb;

    /**
     * @var string that represents the prefix which comes before the invoice number
     *
     * @ORM\Column(name="invoice_prefix", type="text")
     */
    private $invoiceNumberPrefix;

    /**
     * @var int that resembles the version of this invoice.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string that provides any possible context for the VAT
     *
     * @ORM\Column(type="text")
     */
    private $vatContext;

    /**
     * @var string The text of the extra discount of the invoice
     *
     * @ORM\Column(name="discount_text", type="text", nullable=true)
     */
    private $discountText;

    /**
     * @var string The text for the automatic discount of the invoice
     *
     * @ORM\Column(name="auto_discount_text", type="text", nullable=true)
     */
    private $autoDiscountText;

    /**
     * @var string that provides any possible context for a reference of a company
     *
     * @ORM\Column(type="string", name="company_reference", nullable=true)
     */
    private $companyReference;

    /**
     * Creates a new invoice
     *
     * @param EntityManager $entityManager The entityManager of the system.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->creationTime = new DateTime();
        $this->setVersion(0);
        $this->setVatContext();
        $this->setTaxFree();

        $this->setNewInvoiceNumber($entityManager);
    }

    /**
     * @return int
     */
    public function getInvoiceNb()
    {
        return $this->invoiceNb;
    }

    /**
     * @return string
     */
    public function getInvoiceNumberPrefix()
    {
        return $this->invoiceNumberPrefix;
    }

    /**
     * @param  string $prefix
     * @return string
     */
    public function setInvoiceNumberPrefix($prefix)
    {
        $this->invoiceNumberPrefix = $prefix;

        return $this;
    }

    public function setInvoiceNb($invoiceNb)
    {
        if (null === $invoiceNb || !is_numeric($invoiceNb)) {
            throw new InvalidArgumentException('Invalid invoice number: ' . $invoiceNb);
        }

        $this->invoiceNb = (int) $invoiceNb;

        return $this;
    }

    private function setNewInvoiceNumber(EntityManager $entityManager)
    {
        $bookNumber = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_book_number');

        $yearNumber = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_year_number');

        $prefix = $yearNumber . $bookNumber;
        $this->setInvoiceNumberPrefix($prefix);

        $iNb = $entityManager
            ->getRepository('BrBundle\Entity\Invoice')
            ->findNextInvoiceNb($prefix);

        $this->setInvoiceNb($iNb);

        return $this;
    }

    public function getInvoiceNumber()
    {
        return $this->getInvoiceNumberPrefix() . str_pad($this->getInvoiceNb(), 3, '0', STR_PAD_LEFT);
    }

    public function setVatContext($text = '')
    {
        $this->vatContext = $text;

        return $this;
    }

    public function getVatContext()
    {
        return $this->vatContext;
    }

    /**
     * @return string
     */
    public function getDiscountText()
    {
        return $this->discountText;
    }

    /**
     * @param  string $discountText
     * @return self
     */
    public function setDiscountText($discountText)
    {
        $this->discountText = $discountText;

        return $this;
    }

    /**
     * @return string
     */
    public function getAutoDiscountText()
    {
        return $this->autoDiscountText;
    }

    /**
     * @param  string $autoDiscountText
     * @return self
     */
    public function setAutoDiscountText($autoDiscountText)
    {
        $this->autoDiscountText = $autoDiscountText;

        return $this;
    }

    public function setTaxFree($free = false)
    {
        $this->taxFree = $free;

        return $this;
    }

    public function getTaxFree()
    {
        return $this->taxFree;
    }

    public function setCompanyReference($reference)
    {
        $this->companyReference = $reference;

        return $this;
    }

    public function getCompanyReference()
    {
        return $this->companyReference;
    }

    /**
     * @param  int  $version
     * @return self
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
        return (null !== $this->paidTime);
    }

    /**
     * @return self
     * @throws InvalidArgumentException Exception gets thrown if the invoice is already payed.
     */
    public function setPayed()
    {
        if ($this->isPayed()) {
            throw new InvalidArgumentException('This invoice has already been paid');
        }

        $this->paidTime = new DateTime();

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
     * @return bool
     */
    public function isExpired()
    {
        $now = new DateTime();

        return !$this->isPaid() && $now > $this->getExpirationTime();
    }

    /**
     * @return DateTime
     */
    public function getPaidTime()
    {
        return $this->paidTime;
    }

    /**
     * @param  DateTime $paidTime
     * @return self
     */
    public function setPaidTime(DateTime $paidTime)
    {
        if ($this->isPaid()) {
            throw new InvalidArgumentException('This invoice has already been paid');
        }

        $this->paidTime = $paidTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return null !== $this->paidTime;
    }
}
