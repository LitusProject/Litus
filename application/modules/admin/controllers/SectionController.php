<?php

namespace Admin;

use \Admin\Form\Section\Add as AddForm;

use \Litus\Entity\Br\Contracts\Section;

use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Registry;

class SectionController extends \Litus\Controller\Action
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
        $this->view->sectionCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $newSection = new Section(
                    $formData['name'],
                    $formData['content'],
                    $this->getAuthentication()->getPersonObject()
                );
                $this->getEntityManager()->persist($newSection);

                $this->view->sectionCreated = true;
            }
        }
    }

    public function manageAction()
    {
        $paginator = new Paginator(
            new ArrayAdapter($this->getEntityManager()->getRepository('Litus\Entity\Br\Contracts\Section')->findAll())
        );
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }
}