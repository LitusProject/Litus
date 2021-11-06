<?php

namespace CudiBundle\Entity\Article;

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\Article;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\SubjectMap")
 * @ORM\Table(name="cudi_articles_subjects_map")
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
     * @var Article The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Subject The subject of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject", referencedColumnName="id")
     */
    private $subject;

    /**
     * @var AcademicYear The year of the mapping
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
     * @var boolean The flag whether the mapping is just created by a prof
     *
     * @ORM\Column(type="boolean")
     */
    private $isProf;

    /**
     * @var boolean The flag whether the mapping is removed
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @param Article      $article      The article of the mapping
     * @param Subject      $subject      The subject of the mapping
     * @param AcademicYear $academicYear The year of the mapping
     * @param boolean      $mandatory    Flag whether the article is mandatory
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
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return AcademicYear
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
     * @return self
     */
    public function setIsProf($isProf)
    {
        $this->isProf = $isProf;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->removed = true;

        return $this;
    }
}
