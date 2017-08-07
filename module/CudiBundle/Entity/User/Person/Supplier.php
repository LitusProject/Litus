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

namespace CudiBundle\Entity\User\Person;


use CudiBundle\Entity\Supplier as SupplierEntity,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an supplier person.
 *
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\User\Person\Supplier")
 * @ORM\Table(name="users.people_suppliers")
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
