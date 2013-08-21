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

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $newSection = new Section(
                    $this->getEntityManager(),
                    $formData['name'],
                    $formData['invoice_description'],
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

    public function editAction()
    {
        $section = $this->_getSection();

        $form = new EditForm($this->getEntityManager(), $section);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $section->setName($formData['name'])
                    ->setContent($formData['content'])
                    ->setPrice($formData['price'])
                    ->setVatType($this->getEntityManager(), $formData['vat_type'])
                    ->setInvoiceDescription($formData['invoice_description']);

                $this->getEntityManager()->flush();


                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The section was succesfully updated!'
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($section = $this->_getSection()))
            return new ViewModel();

        $this->getEntityManager()->remove($section);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getSection()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the section!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_section',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $section = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract\Section')
            ->findOneById($this->getParam('id'));

        if (null === $section) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No section with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_section',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $section;
    }
}
