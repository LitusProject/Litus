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

namespace CudiBundle\Entity\Articles;

use CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Articles\SubjectMap")
 * @ORM\Table(name="cudi.articles_subjects_map")
 */
class SubjectMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Article The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \SyllabusBundle\Entity\Subject The subject of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject", referencedColumnName="id")
     */
    private $subject;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var boolean Flag whether the article is mandatory
     *
     * @ORM\Column(type="boolean")
     */
    private $mandatory;

    /**
     * @var boolean The flag whether the article is just created by a prof
     *
     * @ORM\Column(type="boolean")
     */
    private $isProf;

    /**
     * @var boolean The flag whether the file is removed
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @param \CudiBundle\Entity\Article $article The article of the mapping
     * @param \SyllabusBundle\Entity\Subject $subject The subject of the mapping
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the mapping
     * @param boolean $mandatory Flag whether the article is mandatory
     */
    public function __construct(Article $article, Subject $subject, AcademicYear $academicYear, $mandatory)
    {
        $this->article = $article;
        $this->subject = $subject;
        $this->academicYear = $academicYear;
        $this->mandatory = $mandatory;
        $this->setIsProf(false);
        $this->removed = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @return boolean
     */
    public function isProf()
    {
        return $this->isProf;
    }

    /**
     * @param boolean $isProf
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Articles\SubjectMap
     */
    public function setRemoved()
    {
        $this->removed = true;
        return $this;
    }
}
