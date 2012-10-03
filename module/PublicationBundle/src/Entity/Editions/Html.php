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
    CommonBundle\Component\Util\Url,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    PublicationBundle\Entity\Publication;

/**
 * This is the entity for a publication
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Editions\HtmlEdition")
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
     * @param string $title The title of this edition
     * @param string $html The html of this edition
     * @param string $images The images path of this edition
     */
    public function __construct(Publication $publication, AcademicYear $academicYear, $title, $html)
    {
        parent::__construct($publication, $academicYear, $title);
        $this->html = $html;
    }

    /**
     * @return string The html of this edition
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @return string The location of the images of this edition.
     */
    public function getImagesDirectory()
    {
        return 'public/_publications/' . $this->getAcademicYear()->getCode(true) .
            '/html/' . Url::createSlug($this->getPublication()->getTitle()) . '/' . Url::createSlug($this->getTitle());
    }
}