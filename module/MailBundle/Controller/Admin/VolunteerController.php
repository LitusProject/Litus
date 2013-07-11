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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Volunteer\Mail as MailForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
    Zend\Validator\File\Count as CountValidator,
    Zend\Validator\File\Size as SizeValidator,
    Zend\View\Model\ViewModel;


/**
 * VolunteerController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class VolunteerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    public function sendAction()
    {
        $currentYear = $this->getCurrentAcademicYear();

        $form = new MailForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $body = $formData['message'];

                $part = new Part($body);

                $part->type = Mime::TYPE_TEXT;

                $part->charset = 'utf-8';
                $message = new MimeMessage();
                $message->addPart($part);

                $mail = new Message();
                $mail->setBody($message)
                    ->setFrom($formData['from'])
                    ->setSubject($formData['subject']);

                $mail->addTo($formData['from']);

                $volunteers = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
                    ->findAllByCountMinimum($currentYear, 1);

                foreach ($volunteers as $volunteer) {
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($volunteer['id']);

                    if (!$person->isPraesidium($currentYear)) {
                        $mail->addBcc($person->getEmail(), $person->getFullName());
                    }
                }

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The mail was successfully sent!'
                    )
                );

                $this->redirect()->toRoute(
                    'mail_admin_volunteer',
                    array(
                        'action' => 'send'
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
}
