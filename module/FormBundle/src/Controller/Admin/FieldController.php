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
    FormBundle\Entity\FormField,
    FormBundle\Form\Admin\Field\Add as AddForm,
    FormBundle\Form\Admin\Field\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * FieldController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($formSpecification = $this->_getFormSpecification()))
            return new ViewModel();

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\FormField')
            ->findByForm($formSpecification);

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'fields' => $fields,
            )
        );
    }

    public function addAction()
    {
        if (!($formSpecification = $this->_getFormSpecification()))
            return new ViewModel();

        $form = new AddForm($formSpecification, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $field = new FormField(
                    $formSpecification,
                    'string', // TODO: support more types
                    $formData['label'],
                    $formData['required']
                );

                $formSpecification->addField($field);

                $this->getEntityManager()->persist($field);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The field was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_form_field',
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

    public function editAction()
    {
        if (!($field = $this->_getFormField()))
            return new ViewModel();

        $form = new EditForm($field, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $field->setLabel($formData['label'])
                    ->setRequired($formData['required']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The field was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_form_field',
                    array(
                        'action' => 'manage',
                        'id' => $field->getForm()->getId(),
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

        if (!($field = $this->_getFormField()))
            return new ViewModel();

        $this->getEntityManager()->remove($field);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getFormSpecification()
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
            ->getRepository('FormBundle\Entity\Nodes\FormSpecification')
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

    private function _getFormField()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the field!'
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

        $field = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\FormField')
            ->findOneById($this->getParam('id'));

        if (null === $field) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No field with the given ID was found!'
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

        return $field;
    }
}
