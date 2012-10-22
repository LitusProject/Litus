<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    FormBundle\Form\Admin\Viewer\Add as AddForm,
    FormBundle\Entity\ViewerMap,
    Zend\View\Model\ViewModel;

/**
 * ViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this form!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findByForm($formSpecification);

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'viewers' => $viewers,
            )
        );
    }

    public function addAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this form!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $form = new AddForm($formSpecification, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\Person');
                if ($formData['person_id'] == '') {
                    // No autocompletion used, we assume the username was entered
                    $person = $repository->findOneByUsername($formData['person_name']);
                } else {
                    $person = $repository->findOneById($formData['person_id']);
                }

                $viewer = new ViewerMap(
                    $formSpecification,
                    $person,
                    $formData['edit']
                );

                $this->getEntityManager()->persist($viewer);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The viewer was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_form_viewer',
                    array(
                        'action' => 'manage',
                        'id' => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($viewer = $this->_getViewer()))
            return new ViewModel();

        if (!$viewer->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this form!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $this->getEntityManager()->remove($viewer);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getForm()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the form!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $formSpecification = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Nodes\Form')
            ->findOneById($this->getParam('id'));

        if (null === $formSpecification) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No form with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $formSpecification;
    }

    private function _getViewer()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the viewer!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $viewer = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneById($this->getParam('id'));

        if (null === $viewer) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No viewer with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $viewer;
    }
}
