<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\View\Model\ViewModel;

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
