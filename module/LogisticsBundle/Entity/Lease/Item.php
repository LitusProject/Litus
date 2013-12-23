<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
     * @var int The item's ID
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
     * @var int The barcode of the item
     *
     * @ORM\Column(type="bigint", unique=true)
     */
    private $barcode;

    /**
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
     * @return \LogisticsBundle\Entity\Lease\Item
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
     * @param string $additionalInfo
     * @return \LogisticsBundle\Entity\Lease\Item
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
     * @param integer $barcode
     * @return \LogisticsBundle\Entity\Lease\Item
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }
}