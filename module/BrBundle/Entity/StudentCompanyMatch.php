<?php

namespace BrBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a match between a company and a student.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\StudentCompanyMatch")
 * @ORM\Table(name="br_match")
 */
class StudentCompanyMatch
{

    /**
     * @var int The match's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     *@var Company The company
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id", onDelete="CASCADE")
     */
    private $company;

    /**
     * @var Academic The academic
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The year in which this match was created.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist"})
     * @ORM\JoinColumn(name="year", referencedColumnName="id")
     */
    private $year;

    /**
     * @param Company      $company  The company
     * @param Academic     $academic The academic
     * @param AcademicYear $year     The current academic year.
     */
    public function __construct(Company $company, Academic $academic, AcademicYear $year)
    {
        $this->company = $company;
        $this->academic = $academic;
        $this->year = $year;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    /**
     * Retrieves the academic
     *
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * Changes the academic
     *
     * @param  Academic $academic The new value
     * @return StudentCompanyMatch
     */
    public function setAcademic($academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * Retrieves the year
     *
     * @return AcademicYear
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Changes the year of this match to the given value.
     *
     * @param  AcademicYear $year The new value
     * @return StudentCompanyMatch
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    public function getStudentCv(EntityManager $em, AcademicYear $ay)
    {
        $entry = $em->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademicAndAcademicYear($ay, $this->academic);

        if (is_null($entry)) {
            return false;
        }
        return $entry;
    }
}
