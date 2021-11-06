<?php

namespace MailBundle\Controller\Admin;

use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;

/**
 * BakskeController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class BakskeController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $form = $this->getForm('mail_bakske_mail', array('academicYear' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $editionId = $formData['edition'];

                $edition = $this->getEntityManager()
                    ->getRepository('PublicationBundle\Entity\Edition\Html')
                    ->findOneById($editionId);

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('mail.bakske_mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('mail.bakske_mail_name');

                $recipientGroups = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
                    ->findAllBakskeByAcademicYear($this->getCurrentAcademicYear());
                $totalRecipients = count($recipientGroups);
                $recipientGroups = array_chunk($recipientGroups, 500);

                $part = new Part($edition->getHtml());
                $part->type = Mime::TYPE_HTML;
                $message = new MimeMessage();
                $message->addPart($part);

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody($message)
                    ->setFrom($mailAddress, $mailName)
                    ->setSubject($formData['subject']);

                $mail->addTo($mailAddress, $mailName);

                if ($formData['test']) {
                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('system_administrator_mail');

                    $mail->addTo($mailAddress, 'System Administrator');

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $this->getMailTransport()->send($mail);
                    }
                } else {
                    foreach ($recipientGroups as $recipients) {
                        foreach ($recipients as $recipient) {
                            $mail->addBcc($recipient->getAcademic()->getEmail(), $recipient->getAcademic()->getFullName());
                        }

                        if (getenv('APPLICATION_ENV') != 'development') {
                            $this->getMailTransport()->send($mail);
                        }

                        $mail->setBcc(array());
                    }
                }

                if ($formData['test']) {
                    $this->flashMessenger()->success(
                        'Success',
                        '*TEST MAIL* The mail would have been sent to ' . $totalRecipients . ' people in ' . count($recipientGroups) . ' groups!'
                    );
                } else {
                    $this->flashMessenger()->success(
                        'Success',
                        'The mail was successfully sent to ' . $totalRecipients . ' people in ' . count($recipientGroups) . ' groups!'
                    );
                }

                $this->redirect()->toRoute(
                    'mail_admin_bakske',
                    array(
                        'action' => 'send',
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
