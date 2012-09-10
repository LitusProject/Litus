<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Entity\Syllabus;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Study;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Syllabus\StudyEnrollment")
 * @ORM\Table(name="users.study_enrollment")
 */
class StudyEnrollment
{
    /**
     * @var int The ID of the enrollment
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The person of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The academic year of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \DateTime The study of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(name="study", referencedColumnName="id")
     */
    private $study;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $academic
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \SyllabusBundle\Entity\Study $study
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, Study $study)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
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
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return \SyllabusBundle\Entity\Study
     */
    public function getStudy()
    {
        return $this->study;
    }
}
