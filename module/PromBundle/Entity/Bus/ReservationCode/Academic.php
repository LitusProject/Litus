<?php

namespace PromBundle\Entity\Bus\ReservationCode;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for prom bus reservation codes for academics.
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\ReservationCode\Academic")
 * @ORM\Table(name="prom_buses_reservation_codes_academic")
 */
class Academic extends \PromBundle\Entity\Bus\ReservationCode
{

    /**
     * @var AcademicEntity The academic this code is assigned to.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * @return AcademicEntity
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param  \CommonBundle\Entity\User\Person\Academic $academic
     * @return self
     */
    public function setAcademic(AcademicEntity $academic)
    {
        $this->academic = $academic;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }
}
