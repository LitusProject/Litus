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

use BrBundle\Entity\Collaborator,
    BrBundle\Entity\Company,
    BrBundle\Entity\Contract\ContractEntry,
    BrBundle\Entity\Product\Order,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * This is the entity for a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract")
 * @ORM\Table(
 *       name="br.contracts",
 *       uniqueConstraints={@ORM\UniqueConstraint(name="contract_unique", columns={"author", "contract_nb"})}
 *      )
 */
class Contract
{
    /**
     * @var int The contract's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Order The contract accompanying this order
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="product_order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var DateTime The date and time when this contract was written
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var Collaborator The author of this contract
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Collaborator")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var Company The company for which this contract is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var ArrayCollection The sections this contract contains
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Contract\ContractEntry", mappedBy="contract", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $contractEntries;

    /**
     * @var string The title of the contract
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The text of the extra discount of the contract
     *
     * @ORM\Column(name="discount_text", type="text", nullable=true)
     */
    private $discountText;

    /**
     * @var string The text for the automatic discount of the contract
     *
     * @ORM\Column(name="auto_discount_text", type="text", nullable=true)
     */
    private $autoDiscountText;

    /**
     * @var string The paymentdetails of the contract
     *
     * @ORM\Column(name="payment_details", type="text", nullable=true)
     */
    private $paymentDetails;

    /**
     * @var int The paymentdays of the contract
     *
     * @ORM\Column(name="payment_days", type="integer", options={"default" = 30})
     */
    private $paymentDays;

    /**
     * @var int The invoice number; -1 indicates that the contract hasn't been signed yet
     *
     * @ORM\Column(name="invoice_nb", type="integer")
     */
    private $invoiceNb;

    /**
     * @var int The contract number. A form of identification that means something to the human users.
     *
     * @ORM\Column(name="contract_nb", type="integer")
     */
    private $contractNb;

    /**
     * @var bool True if the contract has been updated but the updated version has not been generated yet.
     *
     * @ORM\Column(type="boolean")
     */
    private $dirty;

    /**
     * @var bool True if the contract has been signed or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $signed;

    /**
     * @var Integer that resembles the version of this contract.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Order        $order   The order of this contract
     * @param Collaborator $author  The author of this contract
     * @param Company      $company The company for which this contract is meant
     * @param string       $title   The title of the contract
     */
    public function __construct(Order $order, Collaborator $author, Company $company, $title)
    {
        $this->setOrder($order);
        $this->setDate();
        $this->setAuthor($author);
        $this->setCompany($company);
        $this->setTitle($title);
        $this->setVersion(0);

        $this->setDirty();
        $this->setInvoiceNb();

        $this->contractEntries = new ArrayCollection();
        $this->signed = false;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $versionNb
     */
    public function setVersion($versionNb)
    {
        $this->version = $versionNb;
    }

    /**
     * @return int
     */
    public function getPaymentDays()
    {
        return $this->paymentDays;
    }

    /**
     * @param  int  $paymentDays
     * @return self
     */
    public function setPaymentDays($paymentDays)
    {
        $this->paymentDays = $paymentDays;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param  string $paymentDetails
     * @return self
     */
    public function setPaymentDetails($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
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
     * @param  string $discountText
     * @return self
     */
    public function setAutoDiscountText($autoDiscountText)
    {
        $this->autoDiscountText = $autoDiscountText;

        return $this;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return self
     */
    public function setDate()
    {
        $this->date = new DateTime();

        return $this;
    }

    /**
     * @return Collaborator
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @throws InvalidArgumentException
     * @param  Collaborator             $author
     * @return self
     */
    public function setAuthor(Collaborator $author)
    {
        if (null === $author) {
            throw new InvalidArgumentException('Author cannot be null');
        }

        $this->author = $author;

        return $this;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @throws InvalidArgumentException
     * @param  Company                  $company
     * @return self
     */
    public function setCompany(Company $company)
    {
        if (null === $company) {
            throw new InvalidArgumentException('Company cannot be null');
        }

        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @throws InvalidArgumentException
     * @param  string                   $title The title of the contract
     * @return self
     */
    public function setTitle($title)
    {
        if (null === $title || !is_string($title)) {
            throw new InvalidArgumentException('Invalid title');
        }

        $this->title = $title;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * @param  bool $dirty
     * @return self
     */
    public function setDirty($dirty = true)
    {
        $this->dirty = ($dirty ? true : false);

        return $this;
    }

    /**
     * @return bool
     */
    public function isSigned()
    {
        return $this->signed;
    }

    /**
     * @param  bool $signed
     * @return self
     */
    public function setSigned($signed = true)
    {
        $this->signed = $signed;

        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceNb()
    {
        return $this->invoiceNb;
    }

    /**
     * @throws InvalidArgumentException
     * @param  int                      $invoiceNb
     * @return self
     */
    public function setInvoiceNb($invoiceNb = -1)
    {
        if (null === $invoiceNb || !is_numeric($invoiceNb)) {
            throw new InvalidArgumentException('Invalid invoice number: ' . $invoiceNb);
        }

        $this->invoiceNb = (int) $invoiceNb;

        return $this;
    }

    /**
     * @return string
     *
     * @note    The contractnumber gets constructed by the following format "AAxYYY"
     *          With AA the $contractStartNb, x the personal number of the collaborator who created the contract and
     *          YYY the number of current contract.
     **/
    public function getFullContractNumber(EntityManager $entityManager)
    {
        $academicYear = $entityManager
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByDate($this->getDate());

        $contractYearCode = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.contract_number_codes')
            )[$academicYear->getCode(true)];

        return $contractYearCode . $this->getAuthor()->getNumber() . str_pad($this->contractNb, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return int
     *
     * @note    Returns the number of the current contract. This number is used to generate the full contract number.
     *
     **/
    public function getContractNb()
    {
        return $this->contractNb;
    }

    /**
     * @param  int  $contractNb
     * @return self
     */
    public function setContractNb($contractNb)
    {
        if (null === $contractNb || !is_numeric($contractNb)) {
            throw new InvalidArgumentException('Invalid contract number: ' . $contractNb);
        }

        $this->contractNb = (int) $contractNb;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllEntries()
    {
        return $this->contractEntries->toArray();
    }

    /**
     * @return array
     * @note   The array that is returned only contains the most recent entries.
     */
    public function getEntries()
    {
        $array = array();

        $entries = $this->getAllEntries();

        foreach ($entries as $entry) {
            if ($entry->getVersion() == $this->version) {
                array_push($array, $entry);
            }
        }

        return $array;
    }

    /**
     * @param  ContractEntry $entry
     * @return self
     */
    public function setEntry(ContractEntry $entry)
    {
        $this->contractEntries->add($entry);

        return $this;
    }
}
