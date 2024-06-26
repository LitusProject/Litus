<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\General\Organization\Unit;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for a permanent article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\InventoryArticle")
 * @ORM\Table(name="logistics_inventory_article")
 */
class InventoryArticle extends AbstractArticle
{
    /**
     * @static
     * @var array Array with all the possible visibility levels
     */
    public static array $VISIBILITIES = array(
        'Post'          => 'Post',
        'Praesidium'    => 'Praesidium',
        'Greater VTK'   => 'Greater VTK',
        'Members'       => 'Members',
    );

    /**
     * @static
     * @var array Array with all the possible states of an article
     */
    public static array $STATES = array(
        'Available'     => 'Available',
        'Missing'       => 'Missing',
        'Broken'        => 'Broken',
        'In repair'     => 'In repair',
        'Inactive'      => 'Inactive',
        'Filthy'        => 'Filthy',
    );

    /**
     * @var integer The article's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private int $id;

    /**
     * @var string The location of storage of this article
     *
     * @ORM\Column(name="location", type="string")
     */
    private string $location;

    /**
     * @var string The spot in the location of storage of this article
     *
     * @ORM\Column(name="spot", type="string")
     */
    private string $spot;

    /**
     * @var InventoryCategory|null The category of this article
     *
     * @ORM\ManyToOne(inversedBy="articles", targetEntity="LogisticsBundle\Entity\InventoryCategory", cascade={"persist"})
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=true)
     */
    private ?InventoryCategory $category;

    /**
     * @var Unit The unit associated with this article
     *
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id", onDelete="CASCADE")
     */
    private Unit $unit;

    /**
     * @var string The visibility of this article
     *
     * @ORM\Column(name="visibility", type="string")
     */
    private string $visibility;

    /**
     * @var string The status of this article
     *
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    /**
     * @var DateTime|null The warranty of this article
     *
     * @ORM\Column(name="warranty_date", type="datetime", nullable=true)
     */
    private ?DateTime $warrantyDate;

    /**
     * @var integer The amount of deposit that has to be paid for this article when rent by an external
     *
     * @ORM\Column(name="deposit", type="integer", options={"default" = 0})
     */
    private int $deposit;

    /**
     * @var integer The amount of rent that has to be paid for this article when rent by an external
     *
     * @ORM\Column(name="rent", type="integer", options={"default" = 0})
     */
    private int $rent;

    public function __construct()
    {
        parent::__construct();
        $this->category = null;
        $this->warrantyDate = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getSpot(): string
    {
        return $this->spot;
    }

    public function setSpot(string $spot): self
    {
        $this->spot = $spot;

        return $this;
    }

    public function getCategory(): ?InventoryCategory
    {
        return $this->category;
    }

    public function setCategory(InventoryCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function setUnit(Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getWarrantyDate(): ?DateTime
    {
        return $this->warrantyDate;
    }

    public function setWarrantyDate(DateTime $warrantyDate): self
    {
        $this->warrantyDate = $warrantyDate;

        return $this;
    }

    public function getDeposit(): int
    {
        return $this->deposit;
    }

    public function setDeposit(int $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getRent(): int
    {
        return $this->rent;
    }

    public function setRent(int $rent): self
    {
        $this->rent = $rent;

        return $this;
    }
}
