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

namespace BrBundle\Entity;

use BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap;
use BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap;
use BrBundle\Entity\Match\Profile;
use BrBundle\Entity\Match\Profile\CompanyProfile;
use BrBundle\Entity\Match\Profile\StudentProfile;
use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a Match between a student and a Company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match")
 * @ORM\Table(name="br_match")
 */
class Match
{
    /**
     * @var integer The profile's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var CompanyMatcheeMap The company-matchee's profiles
     *
     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $companyMatchee;

    /**
     * @var StudentMatcheeMap The student-matchee's profiles
     *
     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap")
     * @ORM\JoinColumn(name="student", referencedColumnName="id")
     */
    private $studentMatchee;

    /**
     * @var integer The percentage of match fit.
     *
     * @ORM\Column(name="match_percentage", type="integer")
     */
    private $matchPercentage;

    /**
     * @param StudentMatcheeMap $student
     * @param CompanyMatcheeMap $company
     */
    public function __construct(StudentMatcheeMap $student, CompanyMatcheeMap $company)
    {
        $this->companyMatchee = $company;
        $this->studentMatchee = $student;
        $this->matchPercentage = ($this->getMatchPercentages($company->getCompanyProfile(), $student->getCompanyProfile()) +
                $this->getMatchPercentages($company->getStudentProfile(), $student->getStudentProfile())) / 2;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CompanyMatcheeMap
     */
    public function getCompanyMatchee()
    {
        return $this->companyMatchee;
    }

    /**
     * @return StudentMatcheeMap
     */
    public function getStudentMatchee()
    {
        return $this->studentMatchee;
    }

    /**
     * @return int
     */
    public function getMatchPercentage()
    {
        return $this->matchPercentage/100;
    }

    /**
     * @param int $matchPercentage
     */
    public function setMatchPercentage($matchPercentage)
    {
        $this->matchPercentage = $matchPercentage*100;
    }

    /**
     * @param Profile $companyProfile
     * @param Profile $studentProfile
     * @return integer
     */
    public function getMatchPercentages(Profile $companyProfile, Profile $studentProfile)
    {
        $companyTraitMaps = $companyProfile->getFeatures()->toArray();
        $studentTraitMaps = $studentProfile->getFeatures()->toArray();

        $studentTraits = array();
        $companyTraits = array();
        foreach ($studentTraitMaps as $trait)
            $studentTraits[] = $trait->getFeature()->getId();
        foreach ($companyTraitMaps as $trait)
            $companyTraits[] = $trait->getFeature()->getId();

        error_log(count($studentTraits). count($companyTraits));

        $positives = 0;
        $negatives =0;
        foreach ($studentTraits as $ST){
            foreach ($companyTraits as $CT){
                if ($ST == $CT) $positives++;
//                if ($ST->isOpposite($CT)) $negatives++;
            }
        }
        return ceil(5000
            + 5000 * $positives / max(count($studentTraits), count($companyTraits))
            - 5000 * $negatives / max(count($studentTraits), count($companyTraits)));
    }
}
