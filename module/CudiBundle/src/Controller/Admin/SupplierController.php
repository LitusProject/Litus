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
 
namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Credential,
    CudiBundle\Entity\Users\People\Supplier as SupplierPerson,
    CudiBundle\Form\Admin\Supplier\Add as AddForm,
    CudiBundle\Form\Admin\Supplier\Edit as EditForm,
	Doctrine\ORM\EntityManager;

/**
 * SupplierController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SupplierController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
		$paginator = $this->paginator()->createFromEntity(
		    'CudiBundle\Entity\Users\People\Supplier',
		    $this->getParam('page'),
		    array(
		        'canLogin' => true
		    )
		);
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
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
                
                $supplier = $this->getEntityManager()
                	->getRepository('CudiBundle\Entity\Supplier')
                	->findOneById($formData['supplier']);

                $newUser = new SupplierPerson(
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
					$formData['sex'],
					$supplier
                );
                $this->getEntityManager()->persist($newUser);
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The supplier was successfully created!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'admin_supplier',
                	array(
                		'action' => 'manage'
                	)
                );
            }
        }
                
        return array(
        	'form' => $form,
        );
    }
    
    public function editAction()
    {
        $user = $this->_getSupplier();
        		
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
                
                $supplier = $this->getEntityManager()
                	->getRepository('CudiBundle\Entity\Supplier')
                	->findOneById($formData['supplier']);

                $user->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number'])
                    ->setSupplier($supplier)
                    ->updateRoles($roles);
                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The supplier was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_supplier',
                	array(
                		'action' => 'manage'
                	)
                );
            }
        }
        
        return array(
        	'form' => $form
        );
    }
    
    public function deleteAction()
	{
		$this->initAjax();

		$user = $this->_getSupplier();

		//$user->disableLogin();
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
	}
    
    public function searchAction()
    {
        $this->initAjax();
        
        switch($this->getParam('field')) {
        	case 'username':
        		$suppliers = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Users\People\Supplier')
        			->findAllByUsername($this->getParam('string'));
        		break;
        	case 'name':
        		$suppliers = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Users\People\Supplier')
        			->findAllByName($this->getParam('string'));
        		break;
        	case 'supplier':
        		$suppliers = $this->getEntityManager()
        			->getRepository('CudiBundle\Entity\Users\People\Supplier')
        			->findAllBySupplierName($this->getParam('string'));
        		break;
        }
        $result = array();
        foreach($suppliers as $supplier) {
        	$item = (object) array();
        	$item->id = $supplier->getId();
        	$item->username = $supplier->getUsername();
        	$item->supplier = $supplier->getSupplier()->getName();
        	$item->name = $supplier->getFullName();
        	$item->email = $supplier->getEmail();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
	
	private function _getSupplier()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the supplier!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_supplier',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $supplier = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Users\People\Supplier')
	        ->findOneById($this->getParam('id'));
		
		if (null === $supplier) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No supplier with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_supplier',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $supplier;
	}
}