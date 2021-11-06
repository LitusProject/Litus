<?php

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study\Combination;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study")
 * @ORM\Table(name="syllabus_studies")
 */
class Study
{
    /**
     * @var integer The ID of the study
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Combination The combination of module groups
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study\Combination", cascade={"persist"})
     * @ORM\JoinColumn(name="combination", referencedColumnName="id")
     */
    private $combination;

    /**
     * @var AcademicYear The year of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Combination
     */
    public function getCombination()
    {
        return $this->combination;
    }

    /**
     * @param  Combination $combination
     * @return self
     */
    public function setCombination(Combination $combination)
    {
        $this->combination = $combination;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->combination !== null) {
            return $this->combination->getTitle();
        }

        return '';
    }

    /**
     * @return integer
     */
    public function getPhase()
    {
        if ($this->combination !== null) {
            return $this->combination->getPhase();
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        if ($this->combination !== null) {
            return $this->combination->getLanguage();
        }

        return '';
    }
}
