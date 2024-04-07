<?php

namespace LogisticsBundle\Entity;

/**
 * The entity for the categories for an inventory article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\InventoryCategory")
 * @ORM\Table(name="logistics_inventory_category")
 */
class InventoryCategory
{
    /**
     * @var integer The category's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private int $id;

    /**
     * @var string The name of the article's category
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var string The description of the article's category
     *
     * @ORM\Column(type="string")
     */
    private string $description;
}
