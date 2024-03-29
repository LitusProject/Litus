<?php

namespace PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Publication")
 * @ORM\Table(name="publications_publications")
 */
class Publication
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of this publication
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @var boolean Indicates whether this publication is history
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $deleted;

    /**
     * Creates a new publication with the given title
     *
     * @param string $title The title of this publication
     */
    public function __construct($title)
    {
        $this->title = $title;
        $this->deleted = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string The title of this publication
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title The new title
     * @return Publication This
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return Publication This
     */
    public function delete()
    {
        $this->deleted = true;

        return $this;
    }
}
