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
    FormBundle\Entity\Nodes\FormSpecification,
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
        $formSpecification = $this->_getFormSpecification();

        if (!$formSpecification) {
            return new ViewModel();
        }

        $now = new DateTime();
        if ($now < $formSpecification->getStartDate() || 
            $now > $formSpecification->getEndDate() || 
            !$formSpecification->isActive())
        {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'This form is currently closed.'
                )
            );

            return new ViewModel(
                array(
                    'specification' => $formSpecification,
                )
            );
        }

        $form = new SpecifiedForm($formSpecification);

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $this->redirect()->toRoute(
                    'form_view',
                    array
                    (
                        'action'   => 'complete',
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
        return new ViewModel();
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
                'index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $booking = $this->getEntityManager()
        ->getRepository('FormBundle\Entity\Nodes\FormSpecification')
        ->findOneById($this->getParam('id'));

        if (null === $booking) {
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

        return $booking;
    }

}