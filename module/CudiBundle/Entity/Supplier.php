<?php

namespace CudiBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Address;
use CommonBundle\Entity\General\Organization;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Supplier")
 * @ORM\Table(name="cudi_suppliers")
 */
class Supplier
{
    /**
     * @var integer The ID of the supplier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the supplier
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The phone number of the supplier
     *
     * @ORM\Column(type="string", name="phone_number", nullable=true)
     */
    private $phoneNumber;

    /**
     * @var Address The address of the supplier
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The vat number of the supplier
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $vatNumber;

    /**
     * @var string The template used for orders
     *
     * @ORM\Column(type="string")
     */
    private $template;

    /**
     * @var boolean Is this supplier the contactperson
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $contact;

    /**
     * @var array The possible templates
     */
    public static $possibleTemplates = array(
        'default' => 'Default',
    );

    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager;

    /**
     * @param  string $template
     * @return boolean
     */
    public static function isValidTemplate($template)
    {
        return array_key_exists($template, self::$possibleTemplates);
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
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     *
     * @return self
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     *
     * @return self
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return self
     */
    public function setTemplate($template)
    {
        if (!self::isValidTemplate($template)) {
            throw new InvalidArgumentException('The template is not valid.');
        }

        $this->template = $template;

        return $this;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param  AcademicYear      $academicYear
     * @param  Organization|null $organization
     * @return integer
     */
    public function getNumberSold(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberBySupplier($this, $academicYear, $organization);
    }

    /**
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNumberDelivered(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Delivery')
            ->findNumberBySupplier($this, $academicYear);
    }

    /**
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNumberOrdered(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Order\Item')
            ->findNumberBySupplier($this, $academicYear);
    }

    /**
     * @param  AcademicYear      $academicYear
     * @param  Organization|null $organization
     * @return integer
     */
    public function getTotalRevenue(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findTotalRevenueBySupplier($this, $academicYear, $organization);
    }

    /**
     * @param  AcademicYear      $academicYear
     * @param  Organization|null $organization
     * @return integer
     */
    public function getTotalPurchase(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findTotalPurchaseBySupplier($this, $academicYear, $organization);
    }

    /**
     * @return boolean
     */
    public function isContact()
    {
        return $this->contact;
    }

    /**
     * @param boolean
     * @param boolean $contact
     *
     * @return self
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }
}
