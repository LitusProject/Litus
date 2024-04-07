<?php

namespace LogisticsBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var InventoryCategory The category of this article
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\InventoryCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private InventoryCategory $category;

    /**
     * @var ArrayCollection The units associated with the article
     *
     * @ORM\ManyToMany(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinTable(
     *       name="inventory_article_unit",
     *       joinColumns={@ORM\JoinColumn(name="inventory_article_id", referencedColumnName="id")},
     *       inverseJoinColumns={@ORM\JoinColumn(name="unit_id", referencedColumnName="id")}
     *  )
     */
    private Collection $units;

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
     * @var DateTime The warranty of this article
     *
     * @ORM\Column(name="warranty_date", type="datetime")
     */
    private DateTime $warrantyDate;

    /**
     * @var integer The amount of deposit that has to be paid for this article when rent by an external
     *
     * @ORM\Column(name="deposit", type="integer")
     */
    private int $deposit;

    /**
     * @var integer The amount of rent that has to be paid for this article when rent by an external
     *
     * @ORM\Column(name="rent", type="integer")
     */
    private int $rent;
}
