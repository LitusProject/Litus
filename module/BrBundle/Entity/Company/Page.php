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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a company's page.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Page")
 * @ORM\Table(name="br.companies_pages")
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
     * @var string The summary of the company
     *
     * @ORM\Column(type="text")
     */
    private $summary;

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
     * @ORM\JoinTable(name="br.page_years",
     *      joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="year_id", referencedColumnName="id")}
     * )
     */
    private $years;

    /**
     * @param Company $company     The company
     * @param string  $summary     The page's summary
     * @param string  $description The page's description
     */
    public function __construct(Company $company, $summary, $description)
    {
        $this->setSummary($summary);
        $this->setDescription($description);
        $this->company = $company;
        $this->years = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public static function isValidSector($sector)
    {
        return array_key_exists($sector, self::$possibleSectors);
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
     * @param  string                        $summary
     * @return \BrBundle\Entity\Company\Page
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param  string                        $description
     * @return \BrBundle\Entity\Company\Page
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
     * @param  array                         $years Sets the years in which this page existed.
     * @return \BrBundle\Entity\Company\Page This
     */
    public function setYears(array $years)
    {
        $this->years = new ArrayCollection($years);

        return $this;
    }
}
