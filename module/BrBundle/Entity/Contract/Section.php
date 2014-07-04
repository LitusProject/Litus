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

namespace BrBundle\Entity\Contract;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * A section represents a part of a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\Section")
 * @ORM\Table(name="br.contracts_sections")
 */
class Section
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
     * @var string The name of this section
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string The content of this section
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var \CommonBundle\Entity\User\Person The author of this section
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var string The academic year in which this section was written
     *
     * @ORM\Column(type="string", length=9)
     */
    private $year;

    /**
     * @var int The price (VAT excluded!) a company has to pay when they agree to this section of the contract
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var string The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are indexes in a configurable
     * array of possible values
     *
     * @ORM\Column(name="vat_type", type="integer")
     */
    private $vatType;

    /**
     * @var string The short description of this section shown in invoices
     *
     * @ORM\Column(name="invoice_description", type="string", nullable=true)
     */
    private $invoiceDescription;

    /**
     * @param string                           $name        The name of this section
     * @param string                           $description The description on the invoice of this section
     * @param string                           $content     The content of this section
     * @param \CommonBundle\Entity\User\Person $author      The author of this section
     * @param int                              $price
     * @param string                           $vatType     see setVatType($vatType)
     */
    public function __construct(EntityManager $entityManager, $name, $description, $content, Person $author, $price, $vatType)
    {
        $this->setName($name);
        $this->setInvoiceDescription($description);
        $this->setContent($content);
        $this->setAuthor($author);
        $this->setPrice($price);
        $this->setVatType($entityManager, $vatType);

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
     * @param  string                            $name The name of this section
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

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
     * @param  Person                            $author The author of this section
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setAuthor(Person $author)
    {
        if (null === $author)
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
     * @param  string                            $content The content of this section
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setContent($content)
    {
        if ((null === $content) || !is_string($content))
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
     * @param  \Doctrine\ORM\EntityManager       $entityManager The EntityManager instance
     * @param  string                            $vatType       The VAT type (e.g. in Belgium: 6%, 12%, 21% ...); the values are 'A','B', ...; a value is valid if the configuration entry 'br.invoice.vat.<value>' exists
     * @throws \InvalidArgumentException
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setVatType(EntityManager $entityManager, $vatType)
    {
        $types = $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.vat_types');
        $types = unserialize($types);
        if (!isset($types[$vatType])) {
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
     * @param  \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @return int
     */
    public function getVatPercentage(EntityManager $entityManager)
    {
        $types =  $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.vat_types');
        $types = unserialize($types);

        return $types[$this->getVatType()];
    }

    /**
     * @param  float                             $price
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setPrice($price)
    {
        if (
            (null === $price)
            || !preg_match('/^[0-9]+.?[0-9]{0,2}$/', $price)
        ) {
            throw new \InvalidArgumentException('Invalid price');
        }

        $this->price = $price * 100;

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
     * @param  string|null                       $description
     * @return \BrBundle\Entity\Contract\Section
     */
    public function setInvoiceDescription($description)
    {
        if ((null === $description) || !is_string($description))
            throw new \InvalidArgumentException('Invalid description');

        $this->invoiceDescription = $description;

        return $this;
    }
}
