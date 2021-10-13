<?php

namespace SyllabusBundle\Entity\Subject;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject\StudentEnrollment")
 * @ORM\Table(name="syllabus_subjects_student_enrollments")
 */
class StudentEnrollment
{
    /**
     * @var integer The ID of the enrollment
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Subject The subject of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject", inversedBy="enrollments")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $subject;

    /**
     * @var AcademicYear The year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var integer The number of students of the enrollment
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @param Subject      $subject
     * @param AcademicYear $academicYear The year of the mapping
     * @param integer      $academicYear The number of students of the enrollment
     */
    public function __construct(Subject $subject, AcademicYear $academicYear, $number)
    {
        $this->subject = $subject;
        $this->academicYear = $academicYear;
        $this->number = $number;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }
}
