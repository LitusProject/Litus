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
 
namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Users\People\Corporate as CorporatePerson,
	BrBundle\Form\Admin\Company\User\Add as AddForm,
	BrBundle\Form\Admin\Company\User\Edit as EditForm,
	CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Credential;

/**
 * ContactController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class UserController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
	{
	    if (!($company = $this->_getCompany()))
	    	return;
	    	
		$paginator = $this->paginator()->createFromEntity(
		    'BrBundle\Entity\Users\People\Corporate',
		    $this->getParam('page'),
		    array(
		        'canLogin' => true,
		        'company' => $company->getId()
		    ),
		    array(
		        'username' => 'ASC'
		    )
		);
        
        return array(
            'supplier' => $supplier,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
        );
    }
    
    public function addAction()
    {
        if (!($company = $this->_getCompany()))
        	return;
        
        $form = new AddForm($this->getEntityManager());
		
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $newUser = new CorporatePerson(
                    $formData['username'],
                    array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('corporate')
                    ),
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
					$formData['sex'],
					$company
                );
                $this->getEntityManager()->persist($newUser);
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The corporate user was successfully created!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'admin_company_user',
                	array(
                		'action' => 'manage',
                		'id' => $supplier->getId(),
                	)
                );
                
                return;
            }
        }
                
        return array(
            'supplier' => $supplier,
        	'form' => $form,
        );
    }
    
    public function editAction()
    {
        if (!($user = $this->_getUser()))
        	return;
        			
        $form = new EditForm($this->getEntityManager(), $user);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
            if ($form->isValid($formData)) {
                $user->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number']);
                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The supplier user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_supplier_user',
                	array(
                		'action' => 'manage',
                		'id' => $user->getSupplier()->getId()
                	)
                );
                
                return;
            }
        }
        
        return array(
            'supplier' => $user->getSupplier(),
        	'form' => $form,
        );
    }
    
    public function deleteAction()
	{
		$this->initAjax();

		if (!($user = $this->_getUser()))
			return;

		$user->disableLogin();
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
	}
    
    private function _getCompany()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the company!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_company',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $company) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No company with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_company',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $company;
    }
	
	private function _getUser()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the company!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_company',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $company = $this->getEntityManager()
	        ->getRepository('BrBundle\Entity\Users\People\Corporate')
	        ->findOneById($this->getParam('id'));
		
		if (null === $company) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No company with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_company',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $company;
	}
}