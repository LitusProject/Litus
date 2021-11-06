<?php

namespace LogisticsBundle\Entity\Lease;

use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for a leaseable item
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Lease\Item")
 * @ORM\Table(name="logistics_leases_items")
 */
class Item
{
    /**
     * @var integer The item's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the item
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string Additional information about the item
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var integer The barcode of the item
     *
     * @ORM\Column(type="bigint", unique=true)
     */
    private $barcode;

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
     * @param  string $name
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
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * @param  string $additionalInfo
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param  integer $barcode
     * @return self
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }
}
