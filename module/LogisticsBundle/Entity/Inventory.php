<?php

namespace LogisticsBundle\Entity;

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
    public function getBarcode()
    {
        return $this->barcode;
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
     * @return self
     */
    public function subtractAmount($amount)
    {
        $this->amount += $amount;
        return $this;
    }
}