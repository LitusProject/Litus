<?php

namespace CudiBundle\Entity\Sale\Session\Restriction;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction\Year")
 * @ORM\Table(name="cudi_sale_sessions_restrictions_year")
 */
class Year extends \CudiBundle\Entity\Sale\Session\Restriction
{
    /**
     * @var array The possible years of a restriction
     */
    public static $possibleYears = array(
        '1' => '1st Bachelor',
        '2' => '2nd Bachelor',
        '3' => '3th Bachelor',
        '4' => '1st Master',
        '5' => '2nd Master',
    );

    /**
     * @var integer The start value of restriction
     *
     * @ORM\Column(type="smallint", name="start_value")
     */
    private $startValue;

    /**
     * @var integer The end value of restriction
     *
     * @ORM\Column(type="smallint", name="end_value")
     */
    private $endValue;

    /**
     * @param Session $session
     */
    public function __construct(Session $session, $startValue, $endValue)
    {
        parent::__construct($session);

        $this->startValue = $startValue;
        $this->endValue = $endValue;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'year';
    }

    /**
     * @return integer
     */
    public function getStartValue()
    {
        return $this->startValue;
    }

    /**
     * @return integer
     */
    public function getEndValue()
    {
        return $this->endValue;
    }

    /**
     * @return string
     */
    public function getReadableValue()
    {
        return self::$possibleYears[$this->startValue] . ' - ' . self::$possibleYears[$this->endValue];
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        $academicYear = AcademicYearUtil::getUniversityYear($entityManager);

        $studies = $entityManager->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $academicYear);

        foreach ($studies as $studyMap) {
            $year = $studyMap->getStudy()->getPhase();
            if (strpos(strtolower($studyMap->getStudy()->getFullTitle()), 'master') !== false) {
                $year += 3;
            }

            if ($year >= $this->startValue && $year <= $this->endValue) {
                return true;
            }
        }

        return false;
    }
}
