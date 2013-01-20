<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sales\Session;

use CudiBundle\Entity\Sales\Session,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Session\Restriction")
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
     * @var \CudiBundle\Entity\Sales\Session The session of the queue item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
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
     * @param \CudiBundle\Entity\Sales\Session $session
     * @param string $type
     * @param string $startValue
     * @param string $endValue
     */
    public function __construct(Session $session, $type, $startValue, $endValue)
    {
        if (!self::isValidType($type))
            throw new \InvalidArgumentException('The type is not valid.');

        $this->session = $session;
        $this->startValue = $startValue;
        $this->endValue = $endValue;
        $this->type = $type;
    }

    /**
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
     * @return \CudiBundle\Entity\Sales\Session
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
        return $this->startValue;
    }

    /**
     * @return string
     */
    public function getEndValue()
    {
        return $this->endValue;
    }
}
