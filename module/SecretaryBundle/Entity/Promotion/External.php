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

namespace SecretaryBundle\Entity\Promotion;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a promotion.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion\External")
 * @ORM\Table(name="general.promotions_external")
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
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year for this promotion.
     * @param string                                    $firstName    The first name to add
     * @param string                                    $lastName     The last name to add
     * @param string                                    $email        The e-mail address to add
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
