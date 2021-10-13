<?php

namespace SecretaryBundle\Entity\Syllabus\Enrollment;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study as StudyEntity;

/**
 * This entity stores the study enrollment.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Syllabus\Enrollment\Study")
 * @ORM\Table(name="secretary_syllabus_enrollments_study")
 */
class Study
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
     * @var StudyEntity The study of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(name="study", referencedColumnName="id", onDelete="CASCADE")
     */
    private $study;

    /**
     * @param Academic    $academic
     * @param StudyEntity $study
     */
    public function __construct(Academic $academic, StudyEntity $study)
    {
        $this->academic = $academic;
        $this->academicYear = $study->getAcademicYear();
        $this->study = $study;
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
     * @return StudyEntity
     */
    public function getStudy()
    {
        return $this->study;
    }
}
