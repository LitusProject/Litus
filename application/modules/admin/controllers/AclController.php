<?php

namespace Admin;

use \Admin\Application\Form\Acl\Add as AddForm;

use \Doctrine\ORM\QueryBuilder;

use \Litus\Entities\Acl\Action as AclAction;
use \Litus\Entities\Acl\Role;

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
        $roles = $this->getEntityManager()->getRepository('Litus\Entities\Acl\Role')->findAll();
        $parentOptions = array();
        foreach($roles as $role) {
            $parentOptions[$role->getName()] = $role->getName();
        }
        $form = new AddForm(array_merge(array('null' => 'No Parent'), $parentOptions));
        $this->view->form = $form;

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $parent = 'null' != $formData['parent'] ? $this->getEntityManager()->getRepository('Litus\Entities\Acl\Role')->findOneByName($formData['parent']) : null;
                $role = new Role($formData['name'], $parent);
                
                $this->getEntityManager()->persist($role);
            }
        }
    }

    public function manageAction()
    {
        $paginator = new Paginator(new ArrayAdapter($this->getEntityManager()->getRepository('Litus\Entities\Acl\Role')->findAll()));
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }
}