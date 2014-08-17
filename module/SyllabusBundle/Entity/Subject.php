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

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Subject")
 * @ORM\Table(
 *    name="syllabus.subjects",
 *    indexes={@ORM\Index(name="subjects_name", columns={"name", "code"})}
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
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\StudentEnrollment", mappedBy="subject")
     */
    private $enrollments;

    public function __construct()
    {
        $this->enrollments = new ArrayCollection;
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
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment->getNumber();
        }

        return 0;
    }

    /**
     * @param  AcademicYear           $academicYear
     * @return StudentEnrollment|null
     */
    public function getEnrollment(AcademicYear $academicYear)
    {
        foreach ($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment;
        }

        return null;
    }
}
