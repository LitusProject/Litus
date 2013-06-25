<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Editions\PdfEdition")
 * @ORM\Table(name="publications.editions_pdf")
 */
class Pdf extends \PublicationBundle\Entity\Edition
{
    /**
     * Creates a new edition with the given title
     *
     * @param \PublicationBundle\Entity\Publication The publication to which this edition belongs
     * @param \CommonBundle\Entity\General\AcademicYear
     * @param string $title The title of this edition
     * @param \DateTime $date The date of this edition
     */
    public function __construct(Publication $publication, AcademicYear $academicYear, $title, DateTime $date)
    {
        parent::__construct($publication, $academicYear, $title, $date);
    }

    private function getBase()
    {
        return '_publications/' . $this->getAcademicYear()->getCode(true) .
            '/pdf/' . Url::createSlug($this->getPublication()->getTitle());
    }

    private function getFile()
    {
        return Url::createSlug($this->getTitle()) . '.pdf';
    }

    public function getDirectory()
    {
        return 'public/' . $this->getBase();
    }

    /**
     * @return string The pdf file of this edition
     */
    public function getFileName()
    {
        return $this->getDirectory() . '/' . $this->getFile();
    }

    /**
     * @return string The url of the file of this edition
     */
    public function getUrl()
    {
        return $this->getBase() . '/' . $this->getFile();
    }
}
