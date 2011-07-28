<?php

namespace Admin;

use \Admin\Form\Acl\Add as AddForm;

use \Litus\Entity\Acl\Action as AclAction;
use \Litus\Entity\Acl\Role;

use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Registry;

class AclController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }

    public function addAction()
    {
        $roles = $this->getEntityManager()->getRepository('Litus\Entity\Acl\Role')->findAll();
        $parentOptions = array();
        foreach ($roles as $role) {
            $parentOptions[$role->getName()] = $role->getName();
        }
        $form = new AddForm($parentOptions);
        $this->view->form = $form;

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

                $this->getEntityManager()->persist(
                    new Role($formData['name'], $parents)
                );
            }
        }
    }

    public function manageAction()
    {
        $paginator = new Paginator(new ArrayAdapter($this->getEntityManager()->getRepository('Litus\Entity\Acl\Role')->findAll()));
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }
}