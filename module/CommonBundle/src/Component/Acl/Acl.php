<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Acl;

use Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder,
	Zend\Cache\Storage\Adapter as CacheAdapter;

/**
 * Extending Zend's ACL implementation to support our own structure,
 * as well as Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Acl extends \Zend\Acl\Acl
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

        $this->_addResources(
            $query->getQuery()->getResult()
        );
    }

    /**
     * Adding all resources retrieved from the database as well as their children.
     *
     * @param array $resources The resources that should be added
     * @return void
     */
    private function _addResources(array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource(
                $resource->getName(),
                (null === $resource->getParent()) ? null : $resource->getParent()->getName()
            );
            
            $this->_addResources($resource->getChildren());
        }
    }

    /**
     * Load roles from the database.
     *
     * @return void
     */
    protected function loadRoles()
    {
        $this->_addRoles(
            $this->_entityManager->getRepository('CommonBundle\Entity\Acl\Role')->findAll()
        );
    }

    /**
     * Adding all roles retrieved from the database.
     *
     * @param array $roles The roles that should be added
     * @return void
     */
    private function _addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $parents = array();
            foreach($role->getParents() as $parentRole) {
                $parents[] = $parentRole->getName();
            }
            
            $this->_acl->addRole(
                $role->getName(),
                (0 == count($role->getParents())) ? null : $parents
            );

            foreach ($role->getActions() as $action) {
                $this->_acl->allow(
                    $role->getName(),
                    $action->getResource()->getName(),
                    $action->getName());
            }
        }
    }
}