<?php

namespace LogisticsBundle\Entity\Lease;

use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for a leaseable item
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Lease\Item")
 * @ORM\Table(name="logistics.lease_items")
 */
class Item
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * The name of the item
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * Additional information about the item
     * @var string
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * The barcode of the item
     * @var int
     *
     * @ORM\Column(type="bigint", unique=true)
     */
    private $barcode;

    /**
     * Create a new Item entity
     * @param string $name The name of the item (preferrably unique)
     * @param int $barcode The barcode of the item
     * @param type $additionalInfo Extra information to show when leasing the item
     */
    public function __construct($name, $barcode, $additionalInfo = '')
    {
        $this->name = $name;
        $this->barcode = $barcode;
        $this->additionalInfo = $additionalInfo;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set additionalInfo
     *
     * @param string $additionalInfo
     * @return Item
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * Get additionalInfo
     *
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * Set barcode
     *
     * @param integer $barcode
     * @return Item
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }
}