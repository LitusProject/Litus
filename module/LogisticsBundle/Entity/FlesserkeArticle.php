<?php

namespace LogisticsBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for an expandable article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\FlesserkeArticle")
 * @ORM\Table(name="logistics_flesserke_article")
 */
class FlesserkeArticle extends AbstractArticle
{
    /**
     * @static
     * @var array Array with all the possible units
     */
    public static array $UNITS = array(
        'kg'     => 'kg',
        'g'      => 'g',
        'l'      => 'l',
        'cl'     => 'cl',
        'ml'     => 'ml',
        'pieces' => 'pieces',
        'bags'   => 'bags',
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
     * @var string The barcode of this article
     *
     * @ORM\Column(name="barcode", type="string")
     */
    private string $barcode;

    /**
     * @var string The unit of measurement in which this article is packed
     *
     * @ORM\Column(name="unit", type="string")
     */
    private string $unit;

    /**
     * @var integer Quantity per unit
     *
     * @ORM\Column(name="per_unit", type="integer")
     */
    private int $perUnit;

    /**
     * @var string The brand name of this article
     *
     * @ORM\Column(name="brand", type="string")
     */
    private string $brand;

    /**
     * @var FlesserkeCategory The category of this article
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\FlesserkeCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private FlesserkeCategory $category;

    /**
     * @var DateTime The earliest expiration date of this article
     *
     * @ORM\Column(name="expiration_date", type="datetime")
     */
    private DateTime $expirationDate;

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getPerUnit(): int
    {
        return $this->perUnit;
    }

    public function setPerUnit(int $perUnit): self
    {
        $this->perUnit = $perUnit;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): FlesserkeCategory
    {
        return $this->category;
    }

    public function setCategory(FlesserkeCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}
