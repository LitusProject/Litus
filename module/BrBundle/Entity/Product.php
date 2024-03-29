<?php

namespace BrBundle\Entity;

use BrBundle\Entity\Event;
use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * A product is something that can be sold to companies.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Product")
 * @ORM\Table(name="br_products")
 */
class Product
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
     * @var string The name of this product
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The description of this product
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string The contractText of this product
     *
     * @ORM\Column(name="contract_text", type="text")
     */
    private $contractText;

    /**
     * @var string The contractText of this product in English
     *
     * @ORM\Column(name="contract_text_en", type="text", options={"default" = "none"})
     */
    private $contractTextEn;

    /**
     * @var Person The author of this product
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var AcademicYear
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id", nullable=false)
     */
    private $academicYear;

    /**
     * @var Event The shift's event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(name="br_event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var DateTime The date of delivery
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var integer The price (VAT excluded!) a company has to pay when they agree to this product of the contract
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var string The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are indexes in a configurable
     *             array of possible values
     *
     * @ORM\Column(name="vat_type", type="integer")
     */
    private $vatType;

    /**
     * @var boolean that reflects if the product is a refund.
     *
     * @ORM\Column(name="refund", type="boolean", options={"default" = false})
     */
    private $refund;

    /**
     * @var boolean that reflects if the current product is still being sold or not.
     *
     * @ORM\Column(name="old", type="boolean")
     */
    private $old;

    /**
     * @var string The short description of this product shown in invoices
     *
     * @ORM\Column(name="invoice_description", type="string", nullable=true)
     */
    private $invoiceDescription;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param Person       $author       The author of this section
     * @param AcademicYear $academicYear The current academicYear
     */
    public function __construct(Person $author, AcademicYear $academicYear)
    {
        $this->setAuthor($author);
        $this->academicYear = $academicYear;
        $this->old = false;
    }

    /**
     * @return self
     */
    public function setOld()
    {
        $this->old = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isOld()
    {
        return $this->old;
    }

    /**
     * @param  boolean $refund the boolean to set
     * @return self
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRefund()
    {
        return $this->refund;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name The name of this section
     * @return self
     */
    public function setName($name)
    {
        if ($name === null || !is_string($name)) {
            throw new InvalidArgumentException('Invalid name');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param  Person $author The author of this section
     * @return self
     */
    public function setAuthor(Person $author)
    {
        if ($author === null) {
            throw new InvalidArgumentException('Invalid author');
        }

        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getContractText($lang = 'nl')
    {
        if ($lang == 'nl') {
            return $this->getContractTextNl();
        }
        if ($lang == 'en') {
            return $this->getContractTextEn();
        }
        return null;
    }

    /**
     * @param string $contractText The content of this section
     * @return self
     */
    public function setContractText($contractText, $lang = 'nl')
    {
        if ($lang == 'nl') {
            return $this->setContractTextNl($contractText);
        }
        if ($lang == 'en') {
            return $this->setContractTextEn($contractText);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getContractTextNl()
    {
        return $this->contractText;
    }

    /**
     * @param string $contractText The content of this section
     * @return self
     */
    public function setContractTextNl($contractText)
    {
        if ($contractText === null || !is_string($contractText)) {
            throw new InvalidArgumentException('Invalid contract text');
        }

        $this->contractText = $contractText;

        return $this;
    }

    /**
     * @return string
     */
    public function getContractTextEn()
    {
        return $this->contractTextEn;
    }

    /**
     * @param  string $contractText The content of this section
     * @return self
     */
    public function setContractTextEn($contractText)
    {
        if ($contractText === null || !is_string($contractText)) {
            throw new InvalidArgumentException('Invalid contract text');
        }

        $this->contractTextEn = $contractText;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @param  string $vatType The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are 'A','B', ...; a value is valid if the configuration entry 'br.invoice.vat.<value>' exists
     * @return self
     */
    public function setVatType($vatType)
    {
        $this->vatType = $vatType;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatType()
    {
        return $this->vatType;
    }

    /**
     * Returns the VAT percentage for this product.
     *
     * @return integer
     */
    public function getVatPercentage()
    {
        $types = unserialize(
            $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.vat_types')
        );

        return $types[$this->getVatType()];
    }

    /**
     * @param  float $price
     * @return self
     */
    public function setPrice($price)
    {
        if ($price === null || !preg_match('/^[0-9]+.?[0-9]{0,2}$/', $price)) {
            throw new InvalidArgumentException('Invalid price');
        }

        $this->price = (int) $price;

        return $this;
    }

    /**
     * @return integer price in cents
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return integer price in cents with sign
     */
    public function getSignedPrice()
    {
        $sign = 1;
        if ($this->isRefund()) {
            $sign = -1;
        }

        return $sign * $this->price;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string $description
     * @return self
     */
    public function setDescription($description)
    {
        if ($description === null || !is_string($description) || $description == '') {
            throw new InvalidArgumentException('Invalid description');
        }

        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceDescription()
    {
        return $this->invoiceDescription;
    }

    /**
     * @param  string $description
     * @return self
     */
    public function setInvoiceDescription($description)
    {
        if ($description === null || !is_string($description)) {
            throw new InvalidArgumentException('Invalid description');
        }

        $this->invoiceDescription = $description;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param  DateTime $deliveryDate
     * @return self
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
