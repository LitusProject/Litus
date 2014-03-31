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

namespace CommonBundle\Component\Acl;

use CommonBundle\Entity\Acl\Resource,
    CommonBundle\Entity\Acl\Role,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder;

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
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     */
    public function __construct(EntityManager $entityManager = null)
    {
        $this->_entityManager = $entityManager;

        $this->loadResources();
        $this->loadRoles();

        unset($this->_entityManager);
    }

    /**
     * Load resources from the database.
     *
     * @return void
     */
    protected function loadResources()
    {
        $query = new QueryBuilder($this->_entityManager);
        $query->select('r')
            ->from('CommonBundle\Entity\Acl\Resource', 'r')
            ->where('r.parent IS NULL');

        foreach ($query->getQuery()->getResult() as $resource)
            $this->_addResource($resource);
    }

    /**
     * Adding a resource retrieved from the database as well as its children.
     *
     * @param  \CommonBundle\Entity\Acl\Resource $resource The resource that should be added
     * @return void
     */
    private function _addResource(Resource $resource)
    {
        $this->addResource(
            $resource->getName(),
            (null === $resource->getParent()) ? null : $resource->getParent()->getName()
        );

        foreach ($resource->getChildren($this->_entityManager) as $childResource)
            $this->_addResource($childResource);
    }

    /**
     * Load roles from the database.
     *
     * @return void
     */
    protected function loadRoles()
    {
        foreach ($this->_entityManager->getRepository('CommonBundle\Entity\Acl\Role')->findAll() as $role)
            $this->_addRole($role);
    }

    /**
     * Add a role retrieved from the database.
     *
     * @param  \CommonBundle\Entity\Acl\Role $role The role that should be added
     * @return void
     */
    private function _addRole(Role $role)
    {
        if ($this->hasRole($role->getName()))
            return;

        $parents = array();
        foreach ($role->getParents() as $parentRole) {
            if (!$this->hasRole($parentRole->getName()))
                $this->_addRole($parentRole);

            $parents[] = $parentRole->getName();
        }

        $this->addRole(
            $role->getName(), $parents
        );

        foreach ($role->getActions($this->_entityManager) as $action) {
            $this->allow(
                $role->getName(),
                $action->getResource()->getName(),
                $action->getName()
            );
        }
    }
}
