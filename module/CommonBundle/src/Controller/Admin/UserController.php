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
	CommonBundle\Entity\Users\Credential,
	CommonBundle\Entity\Users\People\Academic,
	CommonBundle\Form\Admin\User\Add as AddForm,
	CommonBundle\Form\Admin\User\Edit as EditForm;

/**
 * User management.
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */	
class UserController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{	
	    $paginator = $this->paginator()->createFromEntity(
	        'CommonBundle\Entity\Users\People\Academic',
	        $this->getParam('page'),
	        array(
	            'canLogin' => true
	        )
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
		
		$userCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $roles = array();

                $formData['roles'][] = 'guest';
                foreach ($formData['roles'] as $role) {
                    $roles[] = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName($role);
                }

                $newUser = new Academic(
                    $formData['username'],
                    new Credential(
                        'sha512',
                        $formData['credential']
                    ),
                    $roles,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
					$formData['sex']
                );
                $this->getEntityManager()->persist($newUser);
                
                $form = new AddForm(
                	$this->getEntityManager()
                );
                
                $userCreated = true;
            }
        }
        
        $this->getEntityManager()->flush();
        
        return array(
        	'form' => $form,
        	'userCreated' => $userCreated
        );
    }

    public function editAction()
    {
		$user = $this->_getUser();
		
        $form = new EditForm(
        	$this->getEntityManager(), $user
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
            if ($form->isValid($formData)) {
                $roles = array();

                $formData['roles'][] = 'guest';
                foreach ($formData['roles'] as $role) {
                    $roles[] = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName($role);
                }

                $user->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setTelephone($formData['telephone'])
                    ->updateRoles($roles);
                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_user',
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
		$user = $this->_getUser();
        
        if (null !== $this->getParam('confirm')) {
        	if (1 == $this->getParam('confirm')) {
	            $sessions = $this->getEntityManager()
	                ->getRepository('CommonBundle\Entity\Users\Session')
	                ->findByPerson($user->getId());
	            
	            foreach ($sessions as $session) {
	                $session->deactivate();
	            }
	            $user->disableLogin();
				
				$this->getEntityManager()->flush();
				
				$this->flashMessenger()->addMessage(
				    new FlashMessage(
				        FlashMessage::SUCCESS,
				        'Succes',
				        'The user was successfully deleted!'
				    )
				);
				
				$this->redirect()->toRoute(
					'admin_user',
					array(
						'action' => 'manage'
					)
				);
				
				return;
	        } else {
	            $this->redirect()->toRoute(
	            	'admin_user',
	            	array(
	            		'action' => 'manage'
	            	)
	            );
	            
	            return;
	        }
	    }
        
        return array(
        	'user' => $user
        );
    }
    
    public function searchAction()
    {
    	$this->initAjax();
    	
    	switch($this->getParam('field')) {
    		case 'username':
    			$users = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Person')
    				->findAllByUsername($this->getParam('string'));
    			break;
    		case 'name':
    			$users = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Person')
    				->findAllByName($this->getParam('string'));
    			break;
    	}
    	
    	$result = array();
    	foreach($users as $user) {
    		$item = (object) array();
    		$item->id = $user->getId();
    		$item->username = $user->getUsername();
    		$item->fullName = $user->getMetaInfo()->getAuthors();
    		$item->email = $user->getMetaInfo()->getPublishers();
    		
    		$result[] = $item;
    	}
    	
    	return array(
    		'result' => $result,
    	);
    }
    
    private function _getUser()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No ID was given to identify the user!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_user',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $user = $this->getEntityManager()
	        ->getRepository('CommonBundle\Entity\Users\People\Academic')
	        ->findOneById($this->getParam('id'));
		
		if (null === $user) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No user with the given ID was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_user',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $user;
	}    
}