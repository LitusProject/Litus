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

use CommonBundle\Entity\Users\Credential,
	CommonBundle\Entity\Users\People\Academic,
	CommonBundle\FlashMessenger\FlashMessage,
	CommonBundle\Form\Admin\User\Add as AddForm,
	CommonBundle\Form\Admin\User\Edit as EditForm;
	
class UserController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
	    $paginator = $this->createEntityPaginator(
	        'CommonBundle\Entity\Users\People\Academic',
	        array(
	            'canLogin' => true
	        )
	    );
	    
	    return array(
	    	'paginator' => $paginator,
	    	'paginationControl' => $this->createPaginationControl($paginator, true)
	    );
	}
	
    public function addAction()
    {
        $form = new AddForm(
        	$this->getEntityManager()
        );

        $userCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->get('formData');

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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The user was successfully created!'
                    )
                );
                
                $form = new AddForm();
            }
        }
        
        return array(
        	'form' => $form,
        	'userCreated' => $userCreated
        );
    }

    public function editAction()
    {
        $user = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneById($this->getParam('id'));

        $form = new EditForm(
        	$this->getEntityManager(), $user
        );

        $userEdited = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->get('formData');

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
                    ->updateRoles($roles);
                
                $this->broker('flashmessenger')->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute('admin_user', array('action' => 'manage'));
            }
        }
        
        return array(
        	'form' => $form,
        	'userEdited' => $userEdited
        );
    }

    public function deleteAction()
    {
    	$user = null;
        if (null !== $this->getParam('id')) {
            $user = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneById($this->getParam('id'));
        }
        
        $userDeleted = false;
        if (null !== $this->getParam('confirm')) {
        	if (1 == $this->getParam('confirm')) {
	            $sessions = $this->getEntityManager()
	                ->getRepository('CommonBundle\Entity\Users\Session')
	                ->findByPerson($this->getRequest()->getParam('id'));
	            
	            foreach ($sessions as $session) {
	                $session->deactivate();
	            }
	            $user->disableLogin();
	
	            $userDeleted = true;
	        } else {
	            $this->redirect()->toRoute('admin_user', array('action' => 'manage'));
	        }
	    }
        
        return array(
        	'user' => $user,
        	'userDeleted' => $userDeleted
        );
    }
}