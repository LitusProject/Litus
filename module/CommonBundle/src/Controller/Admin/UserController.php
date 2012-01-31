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

                $newCredential = new Credential(
                    'sha512',
                    $formData['credential']
                );

                $newUser = new Academic(
                    $formData['username'],
                    $newCredential,
                    $roles,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
					$formData['sex']
                );
                $this->getEntityManager()->persist($newUser);
                
                $form = new AddForm();
                
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
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the user that you wish to edit!'
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
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No ID was given to identify the user that you wish to delete!'
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
        
        if (null !== $this->getParam('confirm')) {
        	if (1 == $this->getParam('confirm')) {
	            $sessions = $this->getEntityManager()
	                ->getRepository('CommonBundle\Entity\Users\Session')
	                ->findByPerson($this->getRequest()->getParam('id'));
	            
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
}