<?php

namespace Admin;

use \Admin\Form\Acl\Add as AddForm;

use \Litus\Entity\Acl\Action as AclAction;
use \Litus\Entity\Acl\Role;
use \Litus\Entity\Acl\Resource;

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

                $this->view->roleCreated = true;
            }
        }
    }

    public function manageAction()
    {
        $paginator = new Paginator(new ArrayAdapter($this->getEntityManager()->getRepository('Litus\Entity\Acl\Role')->findAll()));
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;

        /**
        $adminResource = new Resource('admin');
        $this->getEntityManager()->persist($adminResource);
        $this->getEntityManager()->flush();

        $aclResource = new Resource('admin.acl',
            $this->getEntityManager()->getRepository('Litus\Entity\Acl\Resource')->findOneByName('admin')
        );
        $this->getEntityManager()->persist($aclResource);
        $authResource = new Resource('admin.auth',
            $this->getEntityManager()->getRepository('Litus\Entity\Acl\Resource')->findOneByName('admin')
        );
        $this->getEntityManager()->persist($authResource);
        $this->getEntityManager()->flush();

        $addAction = new AclAction('add',
            $this->getEntityManager()->getRepository('Litus\Entity\Acl\Resource')->findOneByName('admin.acl')
        );
        $this->getEntityManager()->persist($addAction);
        $manageAction = new AclAction('manage',
            $this->getEntityManager()->getRepository('Litus\Entity\Acl\Resource')->findOneByName('admin.acl')
        );
        $this->getEntityManager()->persist($manageAction);
        $loginAction = new AclAction('login',
            $this->getEntityManager()->getRepository('Litus\Entity\Acl\Resource')->findOneByName('admin.auth')
        );
        $this->getEntityManager()->persist($loginAction);
        $this->getEntityManager()->flush();
        **/
    }
}