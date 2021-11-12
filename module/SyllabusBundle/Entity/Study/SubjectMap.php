<?php

namespace SyllabusBundle\Entity\Study;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study\SubjectMap")
 * @ORM\Table(name="syllabus_studies_subjects_map")
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
     * @var ModuleGroup The module group of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study\ModuleGroup")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $moduleGroup;

    /**
     * @var Subject The subject of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $subject;

    /**
     * @var boolean Flag whether the subject is mandatory
     *
     * @ORM\Column(type="boolean")
     */
    private $mandatory;

    /**
     * @var AcademicYear The year of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param ModuleGroup  $moduleGroup
     * @param Subject      $subject
     * @param boolean      $mandatory
     * @param AcademicYear $academicYear The year of the mapping
     */
    public function __construct(ModuleGroup $moduleGroup, Subject $subject, $mandatory, AcademicYear $academicYear)
    {
        $this->moduleGroup = $moduleGroup;
        $this->subject = $subject;
        $this->mandatory = $mandatory;
        $this->academicYear = $academicYear;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ModuleGroup
     */
    public function getModuleGroup()
    {
        return $this->moduleGroup;
    }

    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param  boolean $mandatory
     * @return self
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
