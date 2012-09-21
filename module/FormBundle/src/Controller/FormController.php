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

namespace FormBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    FormBundle\Entity\Nodes\Form,
    FormBundle\Entity\Nodes\Entry as FormEntry,
    FormBundle\Entity\Entry as FieldEntry,
    FormBundle\Form\SpecifiedForm,
    Zend\View\Model\ViewModel;

/**
 * FormController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $formSpecification = $this->_getForm();

        if (!$formSpecification) {
            return new ViewModel();
        }

        $message = null;

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() ||
            $now > $formSpecification->getEndDate() ||
            !$formSpecification->isActive())
        {
            $message = 'This form is currently closed.';
        }

        $entriesCount = count($this->getEntityManager()
            ->getRepository('FormBundle\Entity\Nodes\Entry')
            ->findAllByForm($formSpecification));

        if ($formSpecification->getMax() != 0 && $entriesCount >= $formSpecification->getMax()) {
            $message = 'This form has reached the maximum number of submissions.';
        }

        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null) {
            $message = 'Please log in to view this form.';
        } else {
            $entriesCount = count($this->getEntityManager()
                ->getRepository('FormBundle\Entity\Nodes\Entry')
                ->findAllByFormAndPerson($formSpecification, $person));

            if (!$formSpecification->isMultiple() && $entriesCount > 0)
                $message = 'You can\'t fill this form more than once.';
        }

        if ($message) {

            return new ViewModel(
                array(
                    'message'       => $message,
                    'specification' => $formSpecification,
                )
            );
        }

        $form = new SpecifiedForm($this->getEntityManager(), $this->getLanguage(), $formSpecification);

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $formEntry = new FormEntry($person, $formSpecification);

                $this->getEntityManager()->persist($formEntry);
                $this->getEntityManager()->flush();

                foreach ($formSpecification->getFields() as $field) {

                    $value = $formData['field-' . $field->getId()];

                    $fieldEntry = new FieldEntry($formEntry, $field, $value);

                    $formEntry->addFieldEntry($fieldEntry);

                    $this->getEntityManager()->persist($fieldEntry);
                }

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'form_view',
                    array
                    (
                        'action'   => 'complete',
                        'id'       => $formSpecification->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'specification' => $formSpecification,
                'form' => $form,
            )
        );
    }

    public function completeAction()
    {
        $formSpecification = $this->_getForm();

        return new ViewModel(
            array(
                'specification' => $formSpecification,
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
                'index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $form = $this->getEntityManager()
        ->getRepository('FormBundle\Entity\Nodes\Form')
        ->findOneById($this->getParam('id'));

        if (null === $form) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No form with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $form;
    }

}