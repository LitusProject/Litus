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

namespace SyllabusBundle\Entity\Study;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Study,
    SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study\SubjectMap")
 * @ORM\Table(name="syllabus.studies_subjects_map")
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
     * @param ModuleGroup  $study
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
