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

use CommonBunle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Admin\Role\Add as AddForm,
	CommonBunle\Entity\Acl\Action as AclAction,
	CommonBunle\Entity\Acl\Role,
	CommonBunle\Entity\Acl\Resource;

class RoleController extends \CommonBundle\Component\Controller\ActionController
{
    public function addAction()
    {
        $form = new AddForm(
        	$this->getEntityManager()
        );

        $roleCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $formData = $this->getRequest()->post()->get('formData');;

            if ($form->isValid($formData)) {
                $parents = array();
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\Acl\Role')
                                ->findOneByName($parent);
                    }
                }

                $newRole = new Role($formData['name'], $parents);
                foreach ($formData['actions'] as $action) {
                    $newRole->allow(
                        $this->getEntityManager()->getRepository('CommonBundle\Entity\Acl\Action')->findOneById($action)
                    );
                }
                $this->getEntityManager()->persist($newRole);

                // Flushing the EM so that new role is displayed
                $this->getEntityManager()->flush();

                $this->_addDirectFlashMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The role was successfully created!'
                    )
                );
                
                $form = new AddForm(
                	$this->getEntityManager()
                );
            }
        }
        
        return array(
        	'form' => $form,
        	'roleCreated' => $roleCreated
        );
    }

    public function manageAction()
    {
    
    }

	public function editAction()
	{
		
	}
	
	public function deleteAction()
	{
		
	}

    public function loadAction()
    {
		$modules = array(
            'application' => array(
                'index' => array(
                    'index'
                )
            ),
			'admin' => array(
                'run' => array(
                    'index', 'queue', 'start', 'stop', 'delete', 'groups'
                ),
				'auth' => array(
					'login', 'logout', 'authenticate'
				),
				'company' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				),
				'contract' => array(
					'index', 'add', 'manage', 'edit', 'delete', 'sign', 'download', 'compose'
				),
				'index' => array(
					'index'
				),
				'role' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				),
				'section' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				),
                'textbook' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				),
				'user' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				)
			),
            'run' => array(
                'index' => array(
                    'index'
                ),
                'group' => array(
                    'index', 'add'
                ),
                'queue' => array(
                    'index', 'add', 'runner'
                ),
                'screen' => array(
                    'index', 'currentlap'
                )
            )
		);
		
		foreach ($modules as $module => $controllers) {
			$repositoryCheck = $this->getEntityManager()
				->getRepository('Litus\Entity\Acl\Resource')
				->findOneByName($module);
	
			if (null === $repositoryCheck) {
				$moduleResource = new Resource($module);
				$this->getEntityManager()->persist($moduleResource);
			} else {
                $moduleResource = $repositoryCheck;
            }
			
			foreach ($controllers as $controller => $actions) {
				$repositoryCheck = $this->getEntityManager()
					->getRepository('Litus\Entity\Acl\Resource')
					->findOneBy(array('name' => $module . '.' . $controller, 'parent' => $module));
	
				if (null === $repositoryCheck) {
					$controllerResource = new Resource(
						$module . '.' . $controller,
						$moduleResource
					);
					$this->getEntityManager()->persist($controllerResource);
				} else {
                    $controllerResource = $repositoryCheck;
                }
				
				foreach ($actions as $action) {
					$repositoryCheck = $this->getEntityManager()
						->getRepository('Litus\Entity\Acl\Action')
						->findOneBy(array('name' => $action, 'resource' => $module . '.' . $controller));
					
					if (null === $repositoryCheck) {
						$actionResource = new AclAction(
							$action,
							$controllerResource
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
                	->findOneBy(array('name' => 'login', 'resource' => 'admin.auth'))
			);
			$guestRole->allow(
				$this->getEntityManager()
            		->getRepository('CommonBundle\Entity\Acl\Action')
                	->findOneBy(array('name' => 'authenticate', 'resource' => 'admin.auth'))
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
	}
}