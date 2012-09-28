<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\PdfEdition")
 * @ORM\Table(name="publications.pdfeditions")
 */
class PdfEdition
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
     * @var string The title of this edition.
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @var string The file where this pdf is located.
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $file;

    /**
     * @var \PublicationBundle\Entity\Publication The publication to which this edition belongs.
     *
     * @ORM\ManyToOne(targetEntity="PublicationBundle\Entity\Publication")
     * @ORM\JoinColumn(name="publication", referencedColumnName="id", nullable=false)
     */
    private $publication;

    /**
     * Creates a new edition with the given title
     *
     * @param string $title The title of this edition
     * @param string $file The file of this edition
     */
    public function __construct(Publication $publication, $title, $file)
    {
        $this->publication = $publication;
        $this->title = $title;
        $this->file = $file;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \PublicationBundle\Entity\Publication The publication of this edition.
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @return string The title of this edition
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string The pdf file of this edition
     */
    public function getFile()
    {
        return $this->file;
    }
}