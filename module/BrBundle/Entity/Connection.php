<?php

namespace BrBundle\Entity;

use BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap;
use BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap;
use BrBundle\Entity\Match\Profile;
use BrBundle\Entity\Match\Wave;
use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a Connection between a student and a Company. Formerly known as Match, but due to php8 this isn't allowed.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Connection")
 * @ORM\Table(name="br_match")
 */
class Connection
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
     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Connection\MatcheeMap\CompanyMatcheeMap")
     * @ORM\JoinColumn(name="company", referencedColumnName="id", onDelete="CASCADE")
     */
    private $companyMatchee;

    /**
     * @var StudentMatcheeMap The student-matchee's profiles
     *
     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Connection\MatcheeMap\StudentMatcheeMap")
     * @ORM\JoinColumn(name="student", referencedColumnName="id", onDelete="CASCADE")
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
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Connection\Wave\WaveMatchMap")
     * @ORM\JoinColumn(name="wave", referencedColumnName="id", nullable=true, onDelete="set null")
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
        $this->matchPercentage = $this->calculateMatchPercentage();
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
     * @return integer
     */
    public function getMatchPercentage()
    {
        return $this->matchPercentage / 100;
    }

    /**
     * @param integer $matchPercentage
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
     * @param EntityManager $em
     * @param AcademicYear  $academicYear
     * @return boolean
     */
    public function doesCompanyHavePage(EntityManager $em, AcademicYear $academicYear)
    {
        $page = $em
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findOneActiveBySlug($this->getCompany()->getSlug(), $academicYear);

        return !is_null($page);
    }

    public function getStudentCV(EntityManager $em, AcademicYear $ay)
    {
        $person = $this->getStudent();
        $entry = $em->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneByAcademicAndAcademicYear($ay, $person);
        if (is_null($entry)) {
            return false;
        }
        return $entry;
    }

    /**
     * @return integer
     */
    public function calculateMatchPercentage()
    {
        return round(
            ($this->getMatchPercentages($this->companyMatchee->getCompanyProfile(), $this->studentMatchee->getCompanyProfile()) + $this->getMatchPercentages($this->companyMatchee->getStudentProfile(), $this->studentMatchee->getStudentProfile())) / 2
        );
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

        $max = 0;
        $studentTraits = array();
        $companyTraits = array();
        foreach ($studentTraitMaps as $trait) {
            $studentTraits[] = array(
                'id'         => $trait->getFeature()->getId(),
                'importance' => $trait->getImportanceWorth()
            );
            $max += $trait->getImportanceWorth() / 100;
        }
        foreach ($companyTraitMaps as $trait) {
            $companyTraits[] = array(
                'id'         => $trait->getFeature()->getId(),
                'bonusIds'   => $trait->getFeature()->getBonus(),
                'malusIds'   => $trait->getFeature()->getMalus(),
                'importance' => $trait->getImportanceWorth()
            );
            $max += $trait->getImportanceWorth() / 100;
        }

        $max = $max == 0 ? 1 : $max;

        $positives = 0;
        $negatives = 0;
        foreach ($studentTraits as $ST) {
            foreach ($companyTraits as $CT) {
                if ($ST['id'] == $CT['id']) {
                    $positives += ($ST['importance'] + $CT['importance']) / 100;
                }
//                if (in_array($ST['id'], $CT['bonusIds'])) {
//                    $positives += ($ST['importance'] + $CT['importance']) / 100;
//                }
                foreach ($CT['bonusIds'] as $bonus) {
                    if ($bonus->getId() == $ST['id']) {
                        $positives += ($ST['importance'] + $CT['importance']) / 100;
                    }
                }
//                if (in_array($ST['id'], $CT['malusIds'])) {
//                    $negatives += ($ST['importance'] + $CT['importance']) / 100;
//                }
                foreach ($CT['malusIds'] as $malus) {
                    if ($malus->getId() == $ST['id']) {
                        $negatives += ($ST['importance'] + $CT['importance']) / 100;
                    }
                }
            }
        }

        $val = ceil(
            5000 + 5000 * ($positives - $negatives) / $max
        );
//        error_log('SO: positives '.$positives. ' and negatives '.$negatives.', max '.$max.', val '.$val);
        if ($val > 10000) {
            $val = 10000;
        }
        if ($val < 0) {
            $val = 0;
        }
        return $val;
    }
}
