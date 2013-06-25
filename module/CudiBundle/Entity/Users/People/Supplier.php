<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Entity\Users\People;

use CommonBundle\Entity\Users\Credential,
    CudiBundle\Entity\Supplier as SupplierEntity,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an supplier person.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Users\People\Supplier")
 * @ORM\Table(name="users.people_suppliers")
 */
class Supplier extends \CommonBundle\Entity\Users\Person
{
    /**
     * @var \CudiBundle\Entity\Supplier The supplier associated with this person
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @param string $username The user's username
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email The user's e-mail address
     * @param string $phoneNumber The user's phone number
     * @param string $sex The users sex
     * @param \CudiBundle\Entity\Supplier $supplier The supplier
     */
    public function __construct($username, array $roles, $firstName, $lastName, $email, $phoneNumber = null, $sex = null, SupplierEntity $supplier)
    {
        parent::__construct($username, $roles, $firstName, $lastName, $email, $phoneNumber, $sex);

        $this->supplier = $supplier;
    }

    /**
     * @return \CudiBundle\Entity\Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param \CudiBundle\Entity\Supplier $supplier
     *
     * @return \CudiBundle\Entity\Users\People\Supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }
}
