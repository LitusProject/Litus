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
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\Restriction\Name")
 * @ORM\Table(name="cudi.sales_session_restriction_name")
 */
class Name extends Restriction
{
    /**
     * @var string The start value of restriction
     *
     * @ORM\Column(type="string", name="start_value")
     */
    private $startValue;

    /**
     * @var string The end value of restriction
     *
     * @ORM\Column(type="string", name="end_value")
     */
    private $endValue;

    /**
     * @param Session $session
     */
    public function __construct(Session $session, $startValue, $endValue)
    {
        parent::__construct($session);

        $this->startValue = strtolower($startValue);
        $this->endValue = strtolower($endValue);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'name';
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
        return $this->startValue . ' - ' . $this->endValue;
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        if (strtolower(substr($person->getLastName(), 0, strlen($this->startValue))) < $this->startValue || strtolower(substr($person->getLastName(), 0, strlen($this->endValue))) > $this->endValue)
            return false;

        return true;
    }
}
