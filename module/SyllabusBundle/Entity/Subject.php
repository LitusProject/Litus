<?php

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject")
 * @ORM\Table(
 *    name="syllabus_subjects",
 *    indexes={@ORM\Index(name="syllabus_subjects_name_code", columns={"name", "code"})}
 * )
 */
class Subject
{
    /**
     * @var integer The ID of the subject
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The code of the subject
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string The name of the subject
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer The semester number of the subject
     *
     * @ORM\Column(type="smallint")
     */
    private $semester;

    /**
     * @var integer The credits of the subject
     *
     * @ORM\Column(type="smallint")
     */
    private $credits;

    /**
     * @var ArrayCollection The enrollments of the subject
     *
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\Subject\StudentEnrollment", mappedBy="subject")
     */
    private $enrollments;

    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param  string $code
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param  integer $semester
     * @return self
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @param  integer $credits
     * @return self
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNbEnrollment(AcademicYear $academicYear)
    {
        foreach ($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear) {
                return $enrollment->getNumber();
            }
        }

        return 0;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \SyllabusBundle\Entity\Subject\StudentEnrollment|null
     */
    public function getEnrollment(AcademicYear $academicYear)
    {
        foreach ($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear) {
                return $enrollment;
            }
        }

        return null;
    }
}
