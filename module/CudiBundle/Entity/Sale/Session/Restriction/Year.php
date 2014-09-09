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

namespace CudiBundle\Entity\Sale\Session\Restriction;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Session,
    CudiBundle\Entity\Sale\Session\Restriction,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction\Year")
 * @ORM\Table(name="cudi.sales_session_restriction_year")
 */
class Year extends Restriction
{
    /**
     * @var array The possible years of a restriction
     */
    public static $POSSIBLE_YEARS = array(
        '1' => '1st Bachelor',
        '2' => '2nd Bachelor',
        '3' => '3th Bachelor',
        '4' => '1st Master',
        '5' => '2nd Master',
    );

    /**
     * @var int The start value of restriction
     *
     * @ORM\Column(type="smallint", name="start_value")
     */
    private $startValue;

    /**
     * @var int The end value of restriction
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
     * @return string
     */
    public function getStartValue()
    {
        return $this->startValue;
    }

    /**
     * @return string
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
        return self::$POSSIBLE_YEARS[$this->startValue] . ' - ' . self::$POSSIBLE_YEARS[$this->endValue];
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        $years = $entityManager->getRepository('SyllabusBundle\Entity\Subject')
            ->getYearsByPerson($person);

        foreach ($years as $year) {
            if ($year >= $this->startValue && $year <= $this->endValue)
                return true;
        }

        return false;
    }
}
