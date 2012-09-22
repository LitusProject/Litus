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
    FormBundle\Entity\Field,
    FormBundle\Entity\Translation,
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
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        $fields = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($formSpecification);

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'fields' => $fields,
            )
        );
    }

    public function addAction()
    {
        if (!($formSpecification = $this->_getForm()))
            return new ViewModel();

        $form = new AddForm($formSpecification, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $field = new Field(
                    $formSpecification,
                    'string', // TODO: support more types
                    $formData['order'],
                    $formData['required']
                );

                $formSpecification->addField($field);

                $this->getEntityManager()->persist($field);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['label_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $field,
                            $language,
                            $formData['label_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

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
        if (!($field = $this->_getField()))
            return new ViewModel();

        $form = new EditForm($field, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $field->setOrder($formData['order'])
                    ->setRequired($formData['required']);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['label_' . $language->getAbbrev()]) {
                        $translation = $field->getTranslation($language, false);

                        if ($translation === null) {
                            $translation = new Translation(
                                $field,
                                $language,
                                $formData['label_' . $language->getAbbrev()]
                            );
                        } else {
                            $translation->setLabel($formData['label_' . $language->getAbbrev()]);
                        }

                        $this->getEntityManager()->persist($translation);
                    } else {
                        // Delete translation if it already exists
                        $translation = $field->getTranslation($language, false);

                        if ($translation !== null) {
                            $this->getEntityManager()->remove($translation);
                        }
                    }
                }

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

        if (!($field = $this->_getField()))
            return new ViewModel();

        // Delete all entered values
        $entries = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByField($field);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        $this->getEntityManager()->remove($field);
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

    private function _getField()
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
            ->getRepository('FormBundle\Entity\Field')
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
