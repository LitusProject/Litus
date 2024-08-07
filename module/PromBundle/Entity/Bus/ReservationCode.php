<?php

namespace PromBundle\Entity\Bus;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for reservationcodes.
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\ReservationCode")
 * @ORM\Table(name="prom_buses_reservation_codes")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="PromBundle\Entity\Bus\ReservationCode\Academic",
 *      "external"="PromBundle\Entity\Bus\ReservationCode\External"
 * })
 */
abstract class ReservationCode
{
    /**
     * @var integer The ID of this guest info
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var AcademicYear The shift's academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var string The code for the passenger.
     *
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @var boolean If the code is used or not.
     *
     * @ORM\Column(name="used", type="boolean")
     */
    private $used;

    public function __construct(AcademicYear $academicYear)
    {
        $this->code = $this->generateCode();
        $this->academicYear = $academicYear;
        $this->used = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * Sets the code as used.
     */
    public function setUsed()
    {
        $this->used = true;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    private function generateCode()
    {
        return $this->generateRandomString();
    }

    /**
     * @return string
     */
    private function generateRandomString($length = 10)
    {
        $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @return string
     */
    abstract public function getEmail();

    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();
}
