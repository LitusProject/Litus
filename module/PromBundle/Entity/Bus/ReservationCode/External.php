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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PromBundle\Entity\Bus\ReservationCode;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for prom bus reservation codes for external people.
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\ReservationCode\Academic")
 * @ORM\Table(name="prom_buses_reservation_codes_external")
 */
class External extends \PromBundle\Entity\Bus\ReservationCode
{
    /**
     * @var string The first name of the owner of this code
     *
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string The last name of the owner of this code
     *
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string The email adress this code is assigned to.
     *
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param  string $firstName
     * @return External
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param  string $lastName
     * @return External
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string $email
     * @return External
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
