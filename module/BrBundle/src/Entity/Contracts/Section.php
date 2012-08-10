<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Contracts;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\Users\Person,
    Doctrine\ORM\EntityManager;

/**
 * A section represents a part of a contract.
 *
 * @Entity(repositoryClass="BrBundle\Repository\Contracts\Section")
 * @Table(name="br.contracts_sections")
 */
class Section
{
    const VAT_CONFIG_PREFIX = 'br.vat';

    /**
     * @var int A generated ID
     *
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var string The name of this section
     *
     * @Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string The content of this section
     *
     * @Column(type="text")
     */
    private $content;

    /**
     * @var \CommonBundle\Entity\Users\Person The author of this section
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var string The academic year in which this section was written
     *
     * @Column(type="string", length=9)
     */
    private $year;

    /**
     * @var int The price (VAT excluded!) a company has to pay when they agree to this section of the contract
     *
     * @Column(type="integer")
     */
    private $price;

    /**
     * @var string The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are 'A','B', ...; a value is valid if the configuration entry 'br.invoice.vat.<value>' exists
     *
     * @Column(name="vat_type", type="string", length=1)
     */
    private $vatType;

    /**
     * @var string The short description of this section shown in invoices
     *
     * @Column(name="invoice_description", type="string", nullable=true)
     */
    private $invoiceDescription;

    /**
     * @param string $name The name of this section
     * @param string $content The content of this section
     * @param \CommonBundle\Entity\Users\Person $author The author of this section
     * @param int $price
     * @param string $vatType see setVatType($vatType)
     */
    public function __construct(EntityManager $entityManager, $name, $content, Person $author, $price, $vatType)
    {
        $this->setName($name);
        $this->setContent($content);
        $this->setAuthor($author);
        $this->setPrice($price);
        $this->setVatType($entityManager, $vatType);

        $this->setInvoiceDescription();

        $this->year = AcademicYear::getAcademicYear();
    }

    /**
     * @return int
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
     * @param string $name The name of this section
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

        $this->name = $name;

        return $this;
    }

    /**
     * @return \Litus\Entity\Users\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \Litus\Entity\Users\Person $author The author of this section
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setAuthor(Person $author)
    {
        if (null === $auth)
            throw new \InvalidArgumentException('Invalid author');

        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content The content of this section
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setContent($content)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid content');

        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $vatType The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are 'A','B', ...; a value is valid if the configuration entry 'br.invoice.vat.<value>' exists
     * @throws \InvalidArgumentException
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setVatType(EntityManager $entityManager, $vatType)
    {
        try {
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue(self::VAT_CONFIG_PREFIX . '.' . $vatType);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid VAT type: ' . $vatType);
        }

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
     * Returns the VAT percentage for this section.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @return int
     */
    public function getVatPercentage(EntityManager $entityManager)
    {
        return intval(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue(self::VAT_CONFIG_PREFIX . '.' . $this->getVatType())
        );
    }

    /**
     * @param int $price
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setPrice($price)
    {
        if (
            (null === $price)
            || !preg_match('/^[0-9]+.?[0-9]{0,2}$/', $price)
        ) {
            throw new \InvalidArgumentException('Invalid price');
        }

        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getInvoiceDescription()
    {
        return $this->invoiceDescription;
    }

    /**
     * @param string|null $description
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function setInvoiceDescription($description)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid description');

        $this->invoiceDescription = $description;

        return $this;
    }
}
