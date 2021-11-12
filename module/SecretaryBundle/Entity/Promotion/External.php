<?php

namespace SecretaryBundle\Entity\Promotion;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a promotion.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion\External")
 * @ORM\Table(name="secretary_promotions_external")
 */
class External extends \SecretaryBundle\Entity\Promotion
{
    /**
     * @var string The first name of this entry
     *
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string The last name of this entry
     *
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string The e-mail address of this entry
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * Creates a new promotion with the given academic.
     *
     * @param AcademicYear $academicYear The academic year for this promotion.
     * @param string       $firstName    The first name to add
     * @param string       $lastName     The last name to add
     * @param string       $email        The e-mail address to add
     */
    public function __construct(AcademicYear $academicYear, $firstName, $lastName, $email)
    {
        parent::__construct($academicYear);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
