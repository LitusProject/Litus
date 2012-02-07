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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Admin\Role\Add as AddForm,
    CommonBundle\Form\Admin\Role\Edit as EditForm,
	CommonBundle\Entity\Acl\Action as AclAction,
	CommonBundle\Entity\Acl\Role,
	CommonBundle\Entity\Acl\Resource;

class RoleController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
		    'CommonBundle\Entity\Acl\Role',
		    $this->getParam('page')
		);
		
		return array(
			'paginator' => $paginator,
			'paginationControl' => $this->paginator()->createControl(true)
		);
	}

    public function addAction()
    {
        $form = new AddForm(
        	$this->getEntityManager()
        );

        $roleCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $parents = array();
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\Acl\Role')
                                ->findOneByName($parent);
                    }
                }
				
				$actions = array();
				if (isset($formData['actions'])) {
				    foreach ($formData['actions'] as $action) {
				        $actions[] = $this->getEntityManager()
				        	->getRepository('CommonBundle\Entity\Acl\Action')
				            ->findOneByName($action);
				    }
				}
				
                $newRole = new Role(
                	$formData['name'], $parents, $actions
                );

                $this->getEntityManager()->persist($newRole);

                // Flushing the EM so that new role is displayed
                $this->getEntityManager()->flush();
                
                $form = new AddForm(
                	$this->getEntityManager()
                );
                
                $roleCreated = true;
            }
        }       
        
        return array(
        	'form' => $form,
        	'roleCreated' => $roleCreated
        );
    }

	public function editAction()
	{
		$role = $this->_getRole();
		
        $form = new EditForm(
        	$this->getEntityManager(), $role
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
            if ($form->isValid($formData)) {
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                        	->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($parent);
                    }
                }
				$role->setParents($parents);
                
                $actions = array();
                if (isset($formData['actions'])) {
                    foreach ($formData['actions'] as $action) {
                        $actions[] = $this->getEntityManager()
                        	->getRepository('CommonBundle\Entity\Acl\Action')
                            ->findOneById($action);
                    }
                }
                $role->setActions($actions);
                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The role was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_role',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
            }
        }
        
        return array(
        	'form' => $form
        );
	}
	
	public function deleteAction()
	{
		$role = $this->_getRole();
		
		if (null !== $this->getParam('confirm')) {
			if (1 == $this->getParam('confirm')) {
		        $users = $this->getEntityManager()
		            ->getRepository('CommonBundle\Entity\Users\Person')
		            ->findByRole($this->getParam('name'));
		        
		        foreach ($users as $user) {
		            $user->removeRole($role);
		        }
				$this->getEntityManager()->remove($role);
				
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
				    new FlashMessage(
				        FlashMessage::SUCCESS,
				        'Succes',
				        'The role was successfully deleted!'
				    )
				);
				
				$this->redirect()->toRoute(
					'admin_role',
					array(
						'action' => 'manage'
					)
				);
				
				return;
		    } else {
		        $this->redirect()->toRoute(
		        	'admin_role',
		        	array(
		        		'action' => 'manage'
		        	)
		        );
		        
		        return;
		    }
		}
		
		return array(
			'role' => $role
		);
	}
	
	public function loadAction()
	{
		$modules = array(
	        'commonbundle' => array(
                'admin_auth' => array(
                	'index', 'authenticate', 'login', 'logout'
                ),
                'admin_dashboard' => array(
                	'index'
                ),
                'admin_role' => array(
                	'index', 'add', 'manage', 'edit', 'delete'
                ),
                'admin_user' => array(
                	'index', 'add', 'manage', 'edit', 'delete'
                )
	        )
		);
		
		foreach ($modules as $module => $routesArray) {
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
		
		$repositoryCheck = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\Acl\Role')
			->findOneByName('guest');
		
		if (null === $repositoryCheck) {
	    	$guestRole = new Role('guest');
	    	$guestRole->allow(
				$this->getEntityManager()
	        		->getRepository('CommonBundle\Entity\Acl\Action')
	            	->findOneBy(array('name' => 'login', 'resource' => 'admin_auth'))
			);
			$guestRole->allow(
				$this->getEntityManager()
	        		->getRepository('CommonBundle\Entity\Acl\Action')
	            	->findOneBy(array('name' => 'authenticate', 'resource' => 'admin_auth'))
			);
	    	$this->getEntityManager()->persist($guestRole);
		}
		
		$repositoryCheck = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\Acl\Role')
			->findOneByName('company');
		
		if (null === $repositoryCheck) {
			$companyRole = new Role(
				'company',
				array(
					$guestRole
				)
			);
			$this->getEntityManager()->persist($companyRole);
		}
		
		$this->flashMessenger()->addMessage(
		    new FlashMessage(
		        FlashMessage::SUCCESS,
		        'Succes',
		        'The ACL information was succesfully loaded into the database!'
		    )
		);
		
		$this->redirect()->toRoute(
			'admin_role',
			array(
				'action' => 'manage'
			)
		);
		
		$this->getEntityManager()->flush();
		
		return;
	}
	
	public function _getRole()
	{
		if (null === $this->getParam('name')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No name was given to identify the role!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_role',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $role = $this->getEntityManager()
	        ->getRepository('CommonBundle\Entity\Acl\Role')
	        ->findOneByName($this->getParam('name'));
		
		if (null === $role) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No role with the given name was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_role',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $role;
	}
}