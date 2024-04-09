<?php

namespace LogisticsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @var ArrayCollection The articles in this category
     *
     * @ORM\OneToMany(mappedBy="category", targetEntity="LogisticsBundle\Entity\InventoryArticle")
     * @ORM\JoinColumn(name="articles", referencedColumnName="id")
     */
    private Collection $articles;

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

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(FlesserkeArticle $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }

        return $this;
    }

    public function removeArticle(FlesserkeArticle $article): self
    {
        if ($this->articles->removeElement($article)) {
            $this->articles->removeElement($article);
        }

        return $this;
    }
}
