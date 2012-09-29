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

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
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
     * @var CommonBundle\Entity\General\AcademicYear
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="year", referencedColumnName="id", nullable=false)
     */
    private $academicYear;

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
    public function __construct(Publication $publication, AcademicYear $academicYear, $title)
    {
        $this->publication = $publication;
        $this->academicYear = $academicYear;
        $this->title = $title;
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
     * @return \CommonBundle\Entity\General\AcademicYear The publication of this edition.
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return string The title of this edition
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function getDirectory()
    {
        return 'public/_publications/' . $this->getAcademicYear()->getCode(true) .
            '/pdf/' . $this->getPublication()->getTitle();
    }

    /**
     * @return string The pdf file of this edition
     */
    public function getFileName()
    {
        return $this->getDirectory() . '/' . $this->getTitle() . '.pdf';
    }
}