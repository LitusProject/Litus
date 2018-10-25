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

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study\Combination;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study")
 * @ORM\Table(name="syllabus.studies")
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
        if (null !== $this->combination) {
            return $this->combination->getTitle();
        }

        return '';
    }

    /**
     * @return int
     */
    public function getPhase()
    {
        if (null !== $this->combination) {
            return $this->combination->getPhase();
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        if (null !== $this->combination) {
            return $this->combination->getLanguage();
        }

        return '';
    }
}
