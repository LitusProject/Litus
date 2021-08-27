<?php

namespace FormBundle\Controller\Manage;

use FormBundle\Entity\Node\Form;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * MailController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MailController extends \FormBundle\Component\Controller\FormController
{
    public function sendAction()
    {
        $this->initAjax();

        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
            return new ViewModel();
        }

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }
        $defaultFromAddress = '';
        if ($formSpecification->getMail()) {
            $defaultFromAddress = $formSpecification->getMail()->getFrom();
        }

        $form = $this->getForm(
            'form_manage_mail_send',
            array(
                'defaultFromAddress' => $defaultFromAddress,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $mailAddress = $formData['from'];

                $entries = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByForm($formSpecification);

                foreach ($entries as $entry) {
                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody($formData['message'])
                        ->setFrom($mailAddress)
                        ->addTo($entry->getPersonInfo()->getEmail(), $entry->getPersonInfo()->getFullName())
                        ->setSubject($formData['subject']);

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $this->getMailTransport()->send($mail);
                    }
                }

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'result' => (object) array('status' => 'success'),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form'   => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'error'),
            )
        );
    }

    /**
     * @return Form|null
     */
    private function getFormEntity()
    {
        $form = $this->getEntityById('FormBundle\Entity\Node\Form');

        if (!($form instanceof Form)) {
            $this->flashMessenger()->error(
                'Error',
                'No form was found!'
            );

            $this->redirect()->toRoute(
                'form_manage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $form->setEntityManager($this->getEntityManager());

        return $form;
    }
}
