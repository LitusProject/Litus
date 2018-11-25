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

namespace CommonBundle\Component\Acl;

use CommonBundle\Entity\Acl\Resource;
use CommonBundle\Entity\Acl\Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Extending Zend's ACL implementation to support our own structure,
 * as well as Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Acl extends \Zend\Permissions\Acl\Acl
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->loadResources();
        $this->loadRoles();

        unset($this->entityManager);
    }

    /**
     * Load resources from the database.
     *
     * @return void
     */
    protected function loadResources()
    {
        $query = new QueryBuilder($this->entityManager);
        $query->select('r')
            ->from('CommonBundle\Entity\Acl\Resource', 'r')
            ->where('r.parent IS NULL');

        foreach ($query->getQuery()->getResult() as $resource) {
            $this->addResourceEntity($resource);
        }
    }

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectParamVarName
    /**
     * Adding a resource retrieved from the database as well as its children.
     *
     * @param  Resource $resource The resource that should be added
     * @return void
     */
    // phpcs:enable
    private function addResourceEntity(Resource $resource)
    {
        $this->addResource(
            $resource->getName(),
            $resource->getParent() === null ? null : $resource->getParent()->getName()
        );

        foreach ($resource->getChildren($this->entityManager) as $childResource) {
            $this->addResourceEntity($childResource);
        }
    }

    /**
     * Load roles from the database.
     *
     * @return void
     */
    protected function loadRoles()
    {
        foreach ($this->entityManager->getRepository('CommonBundle\Entity\Acl\Role')->findAll() as $role) {
            $this->addRoleEntity($role);
        }
    }

    /**
     * Add a role retrieved from the database.
     *
     * @param  Role $role The role that should be added
     * @return void
     */
    private function addRoleEntity(Role $role)
    {
        if ($this->hasRole($role->getName())) {
            return;
        }

        $parents = array();
        foreach ($role->getParents() as $parentRole) {
            if (!$this->hasRole($parentRole->getName())) {
                $this->addRoleEntity($parentRole);
            }

            $parents[] = $parentRole->getName();
        }

        $this->addRole(
            $role->getName(),
            $parents
        );

        foreach ($role->getActions() as $action) {
            $this->allow(
                $role->getName(),
                $action->getResource()->getName(),
                $action->getName()
            );
        }
    }
}
