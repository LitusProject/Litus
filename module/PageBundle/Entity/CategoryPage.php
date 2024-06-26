<?php

namespace PageBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryPage is de page opened when clicked on a category in the menu.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\CategoryPage")
 * @ORM\Table(name="nodes_pages_categorypages")
 */
class CategoryPage
{
    /**
     * @var integer The ID of this category page
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Category The page's category
     *
     * @ORM\OneToOne(targetEntity="PageBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var ArrayCollection The roles that can edit this page
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="nodes_pages_categorypages_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="category_page", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name", onDelete="CASCADE")},
     * )
     */
    private $editRoles;

    public function __construct()
    {
        $this->editRoles = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category $category
     * @return self
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param array $editRoles
     * @return self
     */
    public function setEditRoles(array $editRoles)
    {
        $this->editRoles = new ArrayCollection($editRoles);

        return $this;
    }

    /**
     * @return array
     */
    public function getEditRoles()
    {
        return $this->editRoles->toArray();
    }

    /**
     * Checks whether or not the given user can edit the page.
     *
     * @param Person|null $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if ($person === null) {
            return false;
        }

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor') {
                return true;
            }
        }

        return false;
    }
}
