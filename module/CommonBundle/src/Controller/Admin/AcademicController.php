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
	CommonBundle\Entity\Users\People\Academic,
	CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
	CommonBundle\Form\Admin\Academic\Add as AddForm,
	CommonBundle\Form\Admin\Academic\Edit as EditForm,
	Zend\View\Model\ViewModel;

/**
 * AcademicController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */	
class AcademicController extends \CommonBundle\Component\Controller\ActionController\AdminController
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
	    
	    return new ViewModel(
	        array(
    	    	'paginator' => $paginator,
    	    	'paginationControl' => $this->paginator()->createControl(true),
    	    )
	    );
	}
	
    public function addAction()
    {
        $form = new AddForm(
        	$this->getEntityManager()
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

                $newUser = new Academic(
                    $formData['username'],
                    $roles,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
					$formData['sex'],
					$formData['university_identification']
                );
                
                $newUser->addUniversityStatus(
                	new UniversityStatus(
                		$newUser,
                		$formData['university_status'],
                		$this->getCurrentAcademicYear()
                	)
                );
                
                $newUser->activate(
                	$this->getEntityManager(),
                	$this->getMailTransport(),
                	!$formData['activation_code']
                );
                
                $this->getEntityManager()->persist($newUser);                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The user was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_academic',
                	array(
                		'action' => 'add'
                	)
                );
                
                return new ViewModel();
                
            }
        }
        
        return new ViewModel(
            array(
            	'form' => $form,
            )
        );
    }

    public function editAction()
    {
		if (!($user = $this->_getUser()))
		    return new ViewModel();
		
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
                    ->setPhoneNumber($formData['phone_number'])
                    ->setUniversityIdentification($formData['university_identification'])
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
                	'admin_academic',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return new ViewModel();
            }
        }
        
        return new ViewModel(
            array(
            	'form' => $form
            )
        );
    }

    public function deleteAction()
    {
    	$this->initAjax();
    
		if (!($user = $this->_getUser()))
		    return new ViewModel();
        
        $sessions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Session')
            ->findByPerson($user->getId());
        
        foreach ($sessions as $session) {
            $session->deactivate();
        }
        $user->disableLogin();
		
		$this->getEntityManager()->flush();
		
		return new ViewModel(
		    array(
			    'result' => array('status' => 'success'),
			)
		);
    }
    
    public function typeaheadAction()
    {
        $this->initAjax();
        
        $users = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findAllByNameTypeahead($this->getParam('string'));
        
        $result = array();
        foreach($users as $user) {
        	$item = (object) array();
        	$item->id = $user->getId();
        	$item->value = $user->getFullName() . ' - ' . $user->getUniversityIdentification();
        	$result[] = $item;
        }
        
        return new ViewModel(
            array(
            	'result' => $result,
            )
        );
    }
    
    public function searchAction()
    {
    	$this->initAjax();
    	
    	switch($this->getParam('field')) {
    		case 'username':
    			$users = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Users\People\Academic')
    				->findAllByUsername($this->getParam('string'));
    			break;
    		case 'name':
    			$users = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Users\People\Academic')
    				->findAllByName($this->getParam('string'));
    			break;
    		case 'university_identification':
    			$users = $this->getEntityManager()
    				->getRepository('CommonBundle\Entity\Users\People\Academic')
    				->findAllByUniversityIdentification($this->getParam('string'));
    			break;
    	}
    	
    	$numResults = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('search_max_results');
    	
    	array_splice($users, $numResults);
    	
    	$result = array();
    	foreach($users as $user) {
    		$item = (object) array();
    		$item->id = $user->getId();
    		$item->username = $user->getUsername();
    		$item->universityIdentification = $user->getUniversityIdentification();
    		$item->fullName = $user->getFullName();
    		$item->email = $user->getEmail();
    		
    		$result[] = $item;
    	}
    	
    	return new ViewModel(
    	    array(
        		'result' => $result,
        	)
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
				'admin_academic',
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
				'admin_academic',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $user;
	}    
}