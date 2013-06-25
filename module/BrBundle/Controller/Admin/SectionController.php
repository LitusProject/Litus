<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Contracts\Section,
    BrBundle\Form\Admin\Section\Add as AddForm,
    BrBundle\Form\Admin\Section\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * SectionController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SectionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Contracts\Section',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        $this->view->form = $form;
        $this->view->sectionCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

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
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

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
