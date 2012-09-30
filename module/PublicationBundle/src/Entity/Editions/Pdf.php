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
namespace PublicationBundle\Entity\Editions;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Editions\PdfEdition")
 * @ORM\Table(name="publications.pdfeditions")
 */
class Pdf extends \PublicationBundle\Entity\Edition
{
    /**
     * Creates a new edition with the given title
     *
     * @param string $title The title of this edition
     * @param string $file The file of this edition
     */
    public function __construct(Publication $publication, AcademicYear $academicYear, $title)
    {
        parent::__construct($publication, $academicYear, $title);
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