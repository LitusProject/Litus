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
use BrBundle\Entity\Match\Wave;
use CommonBundle\Entity\User\Person;
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
     * @var Wave\WaveMatchMap The match's wave
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Match\Wave\WaveMatchMap")
     * @ORM\JoinColumn(name="wave", referencedColumnName="id", nullable=true)
     */
    private $wave;

    /**
     * @var boolean True if the student has shown interest.
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $interested;

    /**
     * @param StudentMatcheeMap $student
     * @param CompanyMatcheeMap $company
     */
    public function __construct(StudentMatcheeMap $student, CompanyMatcheeMap $company)
    {
        $this->companyMatchee = $company;
        $this->studentMatchee = $student;
        $this->matchPercentage = round(
            ($this->getMatchPercentages($company->getCompanyProfile(), $student->getCompanyProfile()) + $this->getMatchPercentages($company->getStudentProfile(), $student->getStudentProfile())) / 2
        );
        $this->interested = false;
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
     * @return Company
     */
    public function getCompany()
    {
        return $this->companyMatchee->getCompany();
    }

    /**
     * @return StudentMatcheeMap
     */
    public function getStudentMatchee()
    {
        return $this->studentMatchee;
    }

    /**
     * @return Person
     */
    public function getStudent()
    {
        return $this->studentMatchee->getStudent();
    }

    /**
     * @return int
     */
    public function getMatchPercentage()
    {
        return $this->matchPercentage / 100;
    }

    /**
     * @param int $matchPercentage
     */
    public function setMatchPercentage($matchPercentage)
    {
        $this->matchPercentage = round($matchPercentage * 100);
    }

    /**
     * @param  Wave\WaveMatchMap|null $wave
     * @return self
     */
    public function setWave($wave)
    {
        $this->wave = $wave;

        return $this;
    }

    /**
     * @return Wave\WaveMatchMap
     */
    public function getWave()
    {
        return $this->wave;
    }

    /**
     * @return boolean
     */
    public function isInterested()
    {
        return $this->interested;
    }

    /**
     * @param boolean $interested
     */
    public function setInterested(bool $interested)
    {
        $this->interested = $interested;
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
        foreach ($studentTraitMaps as $trait) {
            $studentTraits[] = $trait->getFeature()->getId();
        }
        foreach ($companyTraitMaps as $trait) {
            $companyTraits[] = $trait->getFeature()->getId();
        }

        error_log(count($studentTraits). count($companyTraits));

        $positives = 0;
        $negatives = 0;
        foreach ($studentTraits as $ST) {
            foreach ($companyTraits as $CT) {
                if ($ST == $CT) {
                    $positives++;
                }
//                if ($ST->isOpposite($CT)) $negatives++;
            }
        }
        return ceil(
            5000 + 5000 * $positives / max(count($studentTraits), count($companyTraits)) - 5000 * $negatives / max(count($studentTraits), count($companyTraits))
        );
    }
}
