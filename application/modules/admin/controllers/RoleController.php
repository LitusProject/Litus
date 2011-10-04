<?php

namespace Admin;

use \Admin\Form\Role\Add as AddForm;

use \Litus\Entity\Acl\Action as AclAction;
use \Litus\Entity\Acl\Role;
use \Litus\Entity\Acl\Resource;

class RoleController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('add');
    }

    public function addAction()
    {
        $form = new AddForm();

        $this->view->form = $form;
        $this->view->roleCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $parents = array();
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                                ->getRepository('Litus\Entity\Acl\Role')
                                ->findOneByName($parent);
                    }
                }

                $newRole = new Role($formData['name'], $parents);
                foreach ($formData['actions'] as $action) {
                    $newRole->allow(
                        $this->getEntityManager()->getRepository('Litus\Entity\Acl\Action')->findOneById($action)
                    );
                }
                $this->getEntityManager()->persist($newRole);

                // Flushing the EM so that new role is displayed
                $this->getEntityManager()->flush();

                $this->view->roleCreated = true;
                $this->view->form = new AddForm();
            }
        }
    }

    public function manageAction()
    {
        $this->view->paginator = $this->_createPaginator('Litus\Entity\Acl\Role');
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
			'admin' => array(
                'run' => array(
                    'index', 'queue', 'groups'
                ),
				'auth' => array(
					'login', 'logout', 'authenticate'
				),
				'company' => array(
					'index', 'add', 'manage', 'edit', 'delete'
				),
				'contract' => array(
					'index', 'add', 'manage', 'edit', 'delete', 'download', 'compose'
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
			->getRepository('Litus\Entity\Acl\Role')
			->findOneByName('guest');
		
		if (null === $repositoryCheck) {
        	$guestRole = new Role('guest');
        	$guestRole->allow(
				$this->getEntityManager()
            		->getRepository('Litus\Entity\Acl\Action')
                	->findOneBy(array('name' => 'login', 'resource' => 'admin.auth'))
			);
			$guestRole->allow(
				$this->getEntityManager()
            		->getRepository('Litus\Entity\Acl\Action')
                	->findOneBy(array('name' => 'authenticate', 'resource' => 'admin.auth'))
			);
        	$this->getEntityManager()->persist($guestRole);
		}
		
		$repositoryCheck = $this->getEntityManager()
			->getRepository('Litus\Entity\Acl\Role')
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