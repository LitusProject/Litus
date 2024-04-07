<?php

namespace LogisticsBundle\Entity;

/**
 * The entity for the categories for a flesserke article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\FlesserkeCategory")
 * @ORM\Table(name="logistics_flesserke_category")
 */
class FlesserkeCategory
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
