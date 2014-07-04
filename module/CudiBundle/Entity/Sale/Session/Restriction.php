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

namespace CudiBundle\Entity\Sale\Session;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Session,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction")
 * @ORM\Table(name="cudi.sales_session_restriction")
 */
class Restriction
{
    /**
     * @var integer The ID of the restriction
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Session The session of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Session", inversedBy="restrictions")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $session;

    /**
     * @var string The type of restriction
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The start value of restriction
     *
     * @ORM\Column(name="start_value", type="string")
     */
    private $startValue;

    /**
     * @var string The end value of restriction
     *
     * @ORM\Column(name="end_value", type="string")
     */
    private $endValue;

    /**
     * @var array The possible states of a queue item
     */
    public static $POSSIBLE_TYPES = array(
        'name' => 'Name',
        'year' => 'Year',
    );

    /**
     * @var array The possible states of a queue item
     */
    public static $POSSIBLE_YEARS = array(
        '1' => '1st Bachelor',
        '2' => '2nd Bachelor',
        '3' => '3th Bachelor',
        '4' => '1st Master',
        '5' => '2nd Master',
    );

    /**
     * @param Session $session
     * @param string  $type
     * @param string  $startValue
     * @param string  $endValue
     */
    public function __construct(Session $session, $type, $startValue, $endValue)
    {
        if (!self::isValidType($type))
            throw new \InvalidArgumentException('The type is not valid.');

        $this->session = $session;
        $this->startValue = strtolower($startValue);
        $this->endValue = strtolower($endValue);
        $this->type = $type;
    }

    /**
     * @param  string  $type
     * @return boolean
     */
    public static function isValidType($type)
    {
        return array_key_exists($type, self::$POSSIBLE_TYPES);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStartValue()
    {
        if ('year' == $this->type)
            return self::$POSSIBLE_YEARS[$this->startValue];
        return $this->startValue;
    }

    /**
     * @return string
     */
    public function getRawStartValue()
    {
        return $this->startValue;
    }

    /**
     * @return string
     */
    public function getEndValue()
    {
        if ('year' == $this->type)
            return self::$POSSIBLE_YEARS[$this->endValue];
        return $this->endValue;
    }

    /**
     * @return string
     */
    public function getRawEndValue()
    {
        return $this->endValue;
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        if ($this->getRawType() == 'year') {
            $years = $entityManager->getRepository('SyllabusBundle\Entity\Subject')
                ->getYearsByPerson($person);
            $yearFound = false;
            foreach ($years as $year) {
                if ($year >= $this->getRawStartValue() && $year <= $this->getRawEndValue()) {
                    $yearFound = true;
                    break;
                }
            }
            if (!$yearFound)
                return false;
        } elseif ($this->getRawType() == 'name') {
            if (strtolower(substr($person->getLastName(), 0, strlen($this->getStartValue()))) < $this->getStartValue())
                return false;
            if (strtolower(substr($person->getLastName(), 0, strlen($this->getEndValue()))) > $this->getEndValue())
                return false;
        }

        return true;
    }
}
