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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company;
use CommonBundle\Entity\General\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a company's page.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Page")
 * @ORM\Table(name="br_companies_pages")
 */
class Page
{
    /**
     * @var string The page's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The description of the company
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Company
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Company", inversedBy="page")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="br_companies_pages_years_map",
     *      joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="year_id", referencedColumnName="id")}
     * )
     */
    private $years;

    /**
     * @var boolean Whether or not this is company should be displayed on the event page
     * (temporary remove after internship fair 20-21)
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $atEvent;

    /**
     * @param Company $company The company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->years = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public static function isValidSector($sector)
    {
        return array_key_exists($sector, Company::POSSIBLE_SECTORS);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param  string $description
     * @return Page
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function hasYear(AcademicYear $academicYear)
    {
        return $this->years->contains($academicYear);
    }

    /**
     * Retrieves the years in which this page existed.
     *
     * @return array The years in which this page existed.
     */
    public function getYears()
    {
        return $this->years->toArray();
    }

    /**
     * @param  array $years Sets the years in which this page existed.
     * @return Page This
     */
    public function setYears(array $years)
    {
        $this->years = new ArrayCollection($years);

        return $this;
    }

    /**
     * @param AcademicYear $year adds a year that the page is visible
     * @return self
     */
    public function addYear(AcademicYear $year)
    {
        $current_years = $this->years;
        $current_years->add($year);
        $this->years = $current_years;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAtEvent()
    {
        if ($this->atEvent === null) {
            return false;
        } else {
            return $this->atEvent;
        }
    }

    /**
     * @param boolean $atEvent Set whether or not this company should be displayed on the event page
     *
     * @return Page This
     */
    public function setAtEvent($atEvent)
    {
        $this->atEvent = $atEvent;

        return $this;
    }
}
