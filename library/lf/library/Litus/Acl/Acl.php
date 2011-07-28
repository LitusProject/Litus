<?php

namespace Litus\Acl;

use \Doctrine\ORM\QueryBuilder;
use \Zend\Cache\Frontend\Core as CacheCore;
use \Zend\Cache\Backend\File as CacheFile;
use \Zend\Acl\Acl as ZendAcl;
use \Zend\Registry;

class Acl
{
    private $_acl;

    /**
     * Initializes a new ACL object.
     */
    public function __construct()
    {
        $cache = new CacheCore(array('lifetime' => 86400, 'automatic_serialization' => true));
        $cache->setBackend(new CacheFile(array('cache_dir' => '../cache/')));

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
     * Load resources from the database, using a DQL query.
     */
    private function loadResources()
    {
        $query = new QueryBuilder(Registry::get('EntityManager'));
        $query->select('r')
                ->from('Litus\Entities\Acl\Resource', 'r')
                ->where('r.parent is NULL');

        $this->addResources($query->getQuery()->useResultCache(true)->getResult());
    }

    /**
     * Add the resources loaded in loadResources to prime instance.
     *
     * @param mixed $resources The resources that should be added
     * @return void
     */
    private function addResources($resources)
    {
        foreach ($resources as $resource) {
            $this->_acl->addResource($resource->getName(), $resource->getParent() == null ? null : $resource->getParent()->getName());
            $this->addResources($resource->getChildren());
        }
    }

    /**
     * Load roles from the database, using a DQL query.
     *
     * @return void
     */
    private function loadRoles()
    {
        $query = new QueryBuilder(Registry::get('EntityManager'));
        $query->select('r')
                ->from('Litus\Entities\Acl\Role', 'r')
                ->where('r.parent is NULL');

        $this->addRoles($query->getQuery()->useResultCache(true)->getResult());
    }

    /**
     * Add the roles loaded in loadRoles to the prime instance, along with their actions. This method also checks
     * whether or not the given roles have children and adds them if that's the case.
     *
     * @param mixed $roles The roles that should be added
     * @return void
     */
    private function addRoles($roles)
    {
        foreach ($roles as $role) {
            $this->_acl->addRole($role->getName(), $role->getParent() == null ? null : $role->getParent()->getName());
            foreach ($role->getActions() as $action) {
                $this->_acl->allow($role->getName(), $action->getResource()->getName(), $action->getName());
            }
            $this->addRoles($role->getChildren());
        }
    }
}
