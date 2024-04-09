<?php

namespace LogisticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for an expandable article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\CGArticle")
 * @ORM\Table(name="logistics_cg_article")
 */
class CGArticle extends AbstractArticle
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
}
