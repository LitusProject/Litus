<?php

namespace LogisticsBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the Inventory
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Inventory")
 * @ORM\Table(name="logistics_inventory")
 */
class Inventory
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", nullable=true)
     */
    private $category;

    /**
     * @var array The possible types of a category
     */
    public static $possibleCategories = array(
        ''                  => '',
        'Frisdrank'         => 'Frisdrank',
        'Alcohol'           => 'Alcohol',
        'Koffie & thee'     => 'Koffie & thee',
        'Voedsel'           => 'Voedsel',
        'Groenten & fruit'  => 'Groenten & fruit',
        'Snacks'            => 'Snacks',
        'Kruiden'           => 'Kruiden',
        'Saus'              => 'Saus',
        'Conserven'         => 'Conserven',
        'Andere'            => 'Andere'
    );

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", nullable=true)
     */
    private $unit;

    /**
     * @var array The possible types of units
     */
    public static $possibleUnits = array(
        ''          => '',
        'kg'        => 'kg',
        'g'         => 'g',
        'l'         => 'l',
        'cl'        => 'cl',
        'ml'        => 'ml',
        'stuks'     => 'stuks',
        'zakjes'    => 'zakjes',
    );

    /**
     * @var integer Quantity per unit
     *
     * @ORM\Column(name="perUnit", type="integer", nullable=true)
     */
    private $perUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string")
     */
    private $barcode;

    /**
     * @var string The name of the article
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var integer The amount left
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var integer The amount reserved
     *
     * @ORM\Column(name="reserved", type="integer", nullable=true)
     */
    private $reserved;

    /**
     * @var string The expiry date
     *
     * @ORM\Column(name="expiry_date", type="string", nullable=true)
     */
    private $expiryDate;

    /**
     * @var DateTime The expiry date
     *
     * @ORM\Column(name="expiry_date_", type="DateTime", nullable=true)
     */
    private $expiryDate_;

    /**
     * Inventory constructor
     */
    public function __construct()
    {
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param $brand
     * @return self
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param $unit
     * @return self
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return int
     */
    public function getPerUnit()
    {
        return $this->perUnit;
    }

    /**
     * @param $perUnit
     * @return self
     */
    public function setPerUnit($perUnit)
    {
        $this->perUnit = $perUnit;
        return $this;
    }

    /**
     * @param $barcode
     * @return self
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param $amount
     * @return self
     */
    public function addAmount($amount)
    {
        $this->amount += $amount;
        return $this;
    }

    /**
     * @param $amount
     * The amount provided is a negative amount, so addition has to be done
     * @return self
     */
    public function subtractAmount($amount)
    {
        $this->amount += $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getReserved()
    {
        return $this->reserved;
    }

    /**
     * @param $reserved
     * @return self
     */
    public function setReserved($reserved)
    {
        $this->reserved = $reserved;
        return $this;
    }

    /**
     * @param $reserved
     * @return self
     */
    public function addReserved($reserved)
    {
        $this->reserved += $reserved;
        return $this;
    }

    /**
     * @param $reserved
     * Only used to deduct reserved when the items are used
     * @return self
     */
    public function subtractReserved($reserved)
    {
        $this->reserved += $reserved;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param $expiry_date
     * @return self
     */
    public function setExpiryDate($expiry_date)
    {
        $this->expiryDate = $expiry_date;
        return $this;
    }
}