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

use BrBundle\Entity\Contract\Section,
    BrBundle\Form\Admin\Section\Add as AddForm,
    BrBundle\Form\Admin\Section\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
            'BrBundle\Entity\Contract\Section',
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

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            // TODO: validator
            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $newSection = new Section(
                    $this->getEntityManager(),
                    $formData['name'],
                    $formData['invoice_description'] == '' ? null : $formData['invoice_description'],
                    $formData['content'],
                    $this->getAuthentication()->getPersonObject(),
                    $formData['price'],
                    $formData['vat_type']
                );

                $this->getEntityManager()->persist($newSection);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The section was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_section',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }
        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    // public function editAction()
    // {
    //     $section = $this->getEntityManager()
    //                 ->getRepository('Litus\Entity\Br\Contracts\Section')
    //                 ->find($this->getRequest()->getParam('id'));

    //     $form = new EditForm($section);

    //     $this->view->form = $form;
    //     $this->view->sectionEdited = false;

    //     if ($this->getRequest()->isPost()) {
    //         $formData = $this->getRequest()->getPost();
    //         $form->setData($formData);

    //         if($form->isValid()) {
    //             $formData = $form->getFormData($formData);

    //             $section->setName($formData['name'])
    //                 ->setContent($formData['content'])
    //                 ->setPrice($formData['price'])
    //                 ->setVatType($formData['vat_type'])
    //                 ->setInvoiceDescription('' == $formData['invoice_description'] ? null : $formData['invoice_description']);

    //             $this->view->sectionEdited = true;
    //         }
    //     }
    // }

    // public function deleteAction()
    // {
    //     if (null !== $this->getRequest()->getParam('id')) {
    //         $section = $this->getEntityManager()
    //             ->getRepository('Litus\Entity\Br\Contracts\Section')
    //             ->findOneById($this->getRequest()->getParam('id'));
    //     } else {
    //         $section = null;
    //     }

    //     $this->view->sectionDeleted = false;

    //     if (null === $this->getRequest()->getParam('confirm')) {
    //         $this->view->section = $section;
    //     } else {
    //         if (1 == $this->getRequest()->getParam('confirm')) {
    //             $this->getEntityManager()->remove($section);
    //             $this->view->sectionDeleted = true;
    //         } else {
    //             $this->_redirect('manage');
    //         }
    //     }
    // }
}
