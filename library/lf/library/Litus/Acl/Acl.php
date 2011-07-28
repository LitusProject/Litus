<?php

namespace Litus\Acl;

use \Doctrine\ORM\QueryBuilder;

use \Zend\Cache\Frontend\Core as CacheCore;
use \Zend\Cache\Backend\File as CacheFile;
use \Zend\Acl\Acl as ZendAcl;
use \Zend\Registry;

class Acl
{
    /**
     * @var \Zend\Acl\Acl The ACL object
     */
    private $_acl = null;

    /**
     * Initializes a new ACL object.
     */
    public function __construct()
    {
        $cache = new CacheCore(
            array(
                 'lifetime' => 86400,
                 'automatic_serialization' => true
            )
        );
        $cache->setBackend(
            new CacheFile(array('cache_dir' => '../cache/'))
        );

        if (!$cache->test('acl')) {
            $this->_acl = new ZendAcl();

            $this->loadResources();
            $this->loadRoles();

            $cache->save($this->_acl, 'acl');
        } else {
            $this->_acl = $cache->load('acl');
        }
    }

    /**
     * Return the Acl object from Zend.
     *
     * @return \Zend\Acl\Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Load resources from the database.
     *
     * @return void
     */
    public function loadResources()
    {
        $query = new QueryBuilder(Registry::get('EntityManager'));
        $query->select('r')
            ->from('Litus\Entity\Acl\Resource', 'r')
            ->where('r.parent IS NULL');

        $this->_addResources(
            $query->getQuery()->useResultCache(true)->getResult()
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
            $this->_acl->addResource(
                $resource->getName(),
                null === $resource->getParent() ? null : $resource->getParent()->getName()
            );
            
            $this->_addResources($resource->getChildren());
        }
    }

    /**
     * Load roles from the database.
     *
     * @return void
     */
    public function loadRoles()
    {
        $this->_addRoles(
            Registry::get('EntityManager')->getRepository('Litus\Entity\Acl\Role')->findAll()
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
                $role->getParents()->isEmpty() ? null : $parents
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