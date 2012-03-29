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
 
namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Entity\Acl\Action as AclAction,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\Acl\Resource,
    CommonBundle\Entity\General\Config,
    Exception;

/**
 * This abstract function should be implemented by all controller that want to provide
 * installation functionality for a bundle.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class InstallerController extends \CommonBundle\Component\Controller\ActionController
{
	/**
	 * Running all installation methods.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->_initConfig();
		$this->_initAcl();
		
		return array(
			'installerReady' => true
		);
	}
	
	/**
	 * Initiliazes all configuration values for the bundle.
	 *
	 * @return void
	 */
	abstract protected function _initConfig();
	
	/**
	 * Initializes the ACL tree for the bundle.
	 *
	 * @return void
	 */
	abstract protected function _initAcl();
	
	/**
	 * Install the config values
	 *
	 * @param array $configs
	 */
	protected function _installConfig($configs)
	{
		foreach($configs as $item) {
			try {
				$config = $this->getEntityManager()
					->getRepository('CommonBundle\Entity\General\Config')
					->getConfigValue($item['key']);
			} catch(Exception $e) {
				$config = new Config($item['key'], $item['value']);
				$config->setDescription($item['description']);
				$this->getEntityManager()->persist($config);
			}
		}
		$this->getEntityManager()->flush();
	}
	
	/**
	 * Install the roles for the Acl
	 *
	 * @param array $roles
	 */
	protected function installRoles($roles = array())
	{
	    foreach($roles as $roleName => $config) {
	        $role = $this->getEntityManager()
	        	->getRepository('CommonBundle\Entity\Acl\Role')
	        	->findOneByName($roleName);
	        
	        
	        $parents = array();
	        foreach($config['parent_roles'] as $name) {
	            $parents[] = $this->getEntityManager()
	            	->getRepository('CommonBundle\Entity\Acl\Role')
	            	->findOneByName($name);
	        }

	        if (null === $role) {
	        	$role = new Role($roleName, $parents);
	        	$this->getEntityManager()->persist($role);
	        } elseif(sizeof($config['parent_roles']) > 0) {
	            $role->setParents($parents);
	        }
	        
	        foreach ($config['actions'] as $resource => $actions) {
	            foreach($actions as $action) {
	            	$action = $this->getEntityManager()
	            		->getRepository('CommonBundle\Entity\Acl\Action')
	            		->findOneBy(array('name' => $action, 'resource' => $resource));
	            	if (! in_array($action, $role->getActions()))
	            	    $role->allow($action);
	            }
	        }
	    }
	    
	    $this->getEntityManager()->flush();
	}
	
	/**
	 * Install the structure for the Acl
	 *
	 * @param array $structure
	 */
	protected function installAclStructure($structure = array())
	{
	    foreach ($structure as $module => $routesArray) {
	    		$repositoryCheck = $this->getEntityManager()
	    			->getRepository('CommonBundle\Entity\Acl\Resource')
	    			->findOneByName($module);
	    
	    		if (null === $repositoryCheck) {
	    			$moduleResource = new Resource($module);
	    			$this->getEntityManager()->persist($moduleResource);
	    		} else {
	                $moduleResource = $repositoryCheck;
	            }
	    		
	    		foreach ($routesArray as $route => $actions) {
	    			$repositoryCheck = $this->getEntityManager()
	    				->getRepository('CommonBundle\Entity\Acl\Resource')
	    				->findOneBy(array('name' => $route, 'parent' => $module));
	    
	    			if (null === $repositoryCheck) {
	    				$routeResource = new Resource(
	    					$route,
	    					$moduleResource
	    				);
	    				$this->getEntityManager()->persist($routeResource);
	    			} else {
	                    $routeResource = $repositoryCheck;
	                }
	    			
	    			foreach ($actions as $action) {
	    				$repositoryCheck = $this->getEntityManager()
	    					->getRepository('CommonBundle\Entity\Acl\Action')
	    					->findOneBy(array('name' => $action, 'resource' => $route));
	    				
	    				if (null === $repositoryCheck) {
	    					$actionResource = new AclAction(
	    						$action,
	    						$routeResource
	    					);
	    					$this->getEntityManager()->persist($actionResource);
	    				}
	    			}
	    		}
	    	}
	    	$this->getEntityManager()->flush();
	}
}