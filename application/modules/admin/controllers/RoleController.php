<?php

namespace Admin;

use \Admin\Form\Acl\Add as AddForm;

use \Litus\Entity\Acl\Action as AclAction;
use \Litus\Entity\Acl\Role;
use \Litus\Entity\Acl\Resource;

use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Registry;

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

                $this->view->roleCreated = true;
            }
        }
    }

    public function manageAction()
    {
        $paginator = new Paginator(new ArrayAdapter($this->getEntityManager()->getRepository('Litus\Entity\Acl\Role')->findAll()));
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }

    public function loadAction()
    {
        $this->broker('viewRenderer')->setNoRender();

        $adminResource = new Resource('admin');
        $this->getEntityManager()->persist($adminResource);

        $indexResource = new Resource('admin.index',
            $adminResource
        );
        $this->getEntityManager()->persist($indexResource);
        $aclResource = new Resource('admin.acl',
            $adminResource
        );
        $this->getEntityManager()->persist($aclResource);
        $authResource = new Resource('admin.auth',
            $adminResource
        );
        $this->getEntityManager()->persist($authResource);
        $userResource = new Resource('admin.user',
            $adminResource
        );
        $this->getEntityManager()->persist($userResource);

        $indexIndexAction = new AclAction('index',
            $indexResource
        );
        $this->getEntityManager()->persist($indexIndexAction);
        $aclIndexAction = new AclAction('index',
            $aclResource
        );
        $this->getEntityManager()->persist($aclIndexAction);
        $aclAddAction = new AclAction('add',
            $aclResource
        );
        $this->getEntityManager()->persist($aclAddAction);
        $loginAction = new AclAction('login',
            $authResource
        );
        $this->getEntityManager()->persist($loginAction);
        $dologinAction = new AclAction('dologin',
            $authResource
        );
        $this->getEntityManager()->persist($dologinAction);
        $logoutAction = new AclAction('logout',
            $authResource
        );
        $this->getEntityManager()->persist($logoutAction);
        $usersIndexAction = new AclAction('index',
            $userResource
        );
        $this->getEntityManager()->persist($usersIndexAction);
        $usersAddAction = new AclAction('add',
            $userResource
        );
        $this->getEntityManager()->persist($usersAddAction);

        $guestRole = new Role('guest');
        $guestRole->allow($loginAction);
        $this->getEntityManager()->persist($guestRole);
    }
}