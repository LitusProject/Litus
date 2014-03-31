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

namespace PublicationBundle\Entity\Edition;

use CommonBundle\Entity\General\AcademicYear,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Edition\HtmlEdition")
 * @ORM\Table(name="publications.editions_html")
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
     * @param \PublicationBundle\Entity\Publication The publication to which this edition belongs
     * @param \CommonBundle\Entity\General\AcademicYear
     * @param string    $title    The title of this edition
     * @param string    $html     The html of this edition
     * @param \DateTime $date     The date of this edition
     * @param string    $fileName The file name of this edition
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
