<?php

namespace Admin;

use \Admin\Form\Section\Add as AddForm;
use \Admin\Form\Section\Edit as EditForm;

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
                    $this->getAuthentication()->getPersonObject(),
					$formData['price'],
					$formData['vat_type']
                );

                if($formData['invoice_description'] == '')
                    $newSection->setInvoiceDescription(null);
                else
                    $newSection->setInvoiceDescription($formData['invoice_description']);
                
                $this->getEntityManager()->persist($newSection);

                $this->view->form = new AddForm();
                $this->view->sectionCreated = true;
            }
        }
    }

    public function manageAction()
    {
        $this->view->paginator = $this->_createPaginator('Litus\Entity\Br\Contracts\Section');
    }

    public function editAction()
    {
        $section = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Br\Contracts\Section')
                    ->find($this->getRequest()->getParam('id'));

        $form = new EditForm($section);
        
        $this->view->form = $form;
        $this->view->sectionEdited = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $section->setName($formData['name'])
                    ->setContent($formData['content'])
                    ->setPrice($formData['price'])
                    ->setVatType($formData['vat_type'])
                    ->setInvoiceDescription('' == $formData['invoice_description'] ? null : $formData['invoice_description']);

                $this->view->sectionEdited = true;
            }
        }
    }

	public function deleteAction()
	{
		if (null !== $this->getRequest()->getParam('id')) {
            $section = $this->getEntityManager()
                ->getRepository('Litus\Entity\Br\Contracts\Section')
                ->findOneById($this->getRequest()->getParam('id'));
        } else {
            $section = null;
        }

        $this->view->sectionDeleted = false;

        if (null === $this->getRequest()->getParam('confirm')) {
            $this->view->section = $section;
        } else {
            if (1 == $this->getRequest()->getParam('confirm')) {
                $this->getEntityManager()->remove($section);
                $this->view->sectionDeleted = true;
            } else {
                $this->_redirect('manage');
            }
        }
	}
}