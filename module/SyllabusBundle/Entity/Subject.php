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
     * @var \Doctrine\Common\Collections\ArrayCollection The enrollments of the subject
     *
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\StudentEnrollment", mappedBy="subject")
     */
    private $enrollments;

    /**
     * @param string $code
     * @param string $name
     * @param integer $semester
     * @param integer $credits
     */
    public function __construct($code, $name, $semester, $credits)
    {
        $this->code = $code;
        $this->name = $name;
        $this->semester = $semester;
        $this->credits = $credits;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \SyllabusBundle\Entity\Subject
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
     * @return integer
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     *
     * @return integer
     */
    public function getNbEnrollment(AcademicYear $academicYear)
    {
        foreach($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment->getNumber();
        }
        return 0;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     *
     * @return \SyllabusBundle\Entity\StudentEnrollment
     */
    public function getEnrollment(AcademicYear $academicYear)
    {
        foreach($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment;
        }
        return null;
    }
}
