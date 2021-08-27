<?php

namespace PublicationBundle\Entity\Edition;

use CommonBundle\Entity\General\AcademicYear;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Edition\Html")
 * @ORM\Table(name="publications_editions_html")
 */
class Html extends \PublicationBundle\Entity\Edition
{
    /**
     * @var string The html of this edition.
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $html;

    /**
     * Creates a new edition with the given title
     *
     * @param Publication  $publication The publication to which this edition belongs
     * @param AcademicYear
     * @param string       $title       The title of this edition
     * @param string       $html        The html of this edition
     * @param DateTime     $date        The date of this edition
     * @param string       $fileName    The file name of this edition
     */
    public function __construct(Publication $publication, AcademicYear $academicYear, $title, $html, DateTime $date, $fileName)
    {
        parent::__construct($publication, $academicYear, $title, $date, $fileName);
        $this->html = $html;
    }

    /**
     * @return string The html of this edition
     */
    public function getHtml()
    {
        return $this->html;
    }
}
