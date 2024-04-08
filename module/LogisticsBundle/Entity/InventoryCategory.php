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
     * @ORM\Column(type="text")
     */
    private string $description;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
