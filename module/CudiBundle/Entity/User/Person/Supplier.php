<?php

namespace CudiBundle\Entity\User\Person;

use CudiBundle\Entity\Supplier as SupplierEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an supplier person.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\User\Person\Supplier")
 * @ORM\Table(name="users_people_suppliers")
 */
class Supplier extends \CommonBundle\Entity\User\Person
{
    /**
     * @var SupplierEntity The supplier associated with this person
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @return SupplierEntity
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param SupplierEntity $supplier
     *
     * @return \CudiBundle\Entity\User\Person\Supplier
     */
    public function setSupplier(SupplierEntity $supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Retrieves all the roles from the academic's units for the
     * latest academic year.
     *
     * @return array
     */
    public function getUnitRoles()
    {
        return array();
    }
}
