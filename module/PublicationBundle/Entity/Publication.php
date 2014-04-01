<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Publication")
 * @ORM\Table(name="publications.publications")
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
     * @return Publication This
     */
    public function delete()
    {
        $this->deleted = true;

        return $this;
    }
}
