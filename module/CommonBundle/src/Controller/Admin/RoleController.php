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
	    $this->initAjax();
	    
    	$role = $this->_getRole();
        
        $users = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findAllByRole($role->getName());
        
        foreach ($users as $user) {
            $user->removeRole($role);
        }
        $this->getEntityManager()->remove($role);
        
        $this->getEntityManager()->flush();
    	
    	return array(
    		'result' => array(
    			'status' => 'success'
    		),
    	);
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