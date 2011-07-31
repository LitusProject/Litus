<?php

namespace Admin;

use \Admin\Form\User\Add as AddForm;

use \Litus\Entity\Users\Credential;
use \Litus\Entity\Users\People\Academic;

class UserController extends \Litus\Controller\Action
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
        $this->view->userCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $roles = array();

                $formData['roles'][] = 'guest';
                foreach ($formData['roles'] as $role) {
                    $roles[] = $this->getEntityManager()
                            ->getRepository('Litus\Entity\Acl\Role')
                            ->findOneByName($role);
                }

                $newCredential = new Credential(
                    'sha512',
                    $formData['credential']
                );
                $this->getEntityManager()->persist($newCredential);

                $newUser = new Academic(
                    $formData['username'],
                    $newCredential,
                    $roles,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email']
                );
                $this->getEntityManager()->persist($newUser);

                $this->view->userCreated = true;
            }
        }
    }
}