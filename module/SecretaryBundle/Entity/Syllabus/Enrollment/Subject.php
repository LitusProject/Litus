<?php

namespace SecretaryBundle\Entity\Syllabus\Enrollment;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Subject as SubjectEntity;

/**
 * This entity stores the subject enrollment.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Syllabus\Enrollment\Subject")
 * @ORM\Table(name="secretary_syllabus_enrollments_subject")
 */
class Subject
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
     * @var Academic The person of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The academic year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var SubjectEntity The subject of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject", referencedColumnName="id")
     */
    private $subject;

    /**
     * @param Academic      $academic
     * @param AcademicYear  $academicYear
     * @param SubjectEntity $subject
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, SubjectEntity $subject)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->subject = $subject;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return SubjectEntity
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
