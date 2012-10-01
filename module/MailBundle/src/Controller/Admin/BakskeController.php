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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Bakske\Mail as SendForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * BakskeController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class BakskeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function sendAction()
    {
        $form = new SendForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                $editionId = $formData['edition'];

                $edition = $this->getEntityManager()
                    ->getRepository('PublicationBundle\Entity\Editions\Html')
                    ->findOneById($editionId);

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('mail.bakske_mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('mail.bakske_mail_name');

                $mail = new Message();
                $mail->setBody($edition->getHtml())
                    ->setFrom($mailAddress, $mailName)
                    ->setSubject($formData['subject']);

                $recipients = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
                    ->findAllBakskeByAcademicYear($this->getCurrentAcademicYear());

                $mail->addTo($mailAddress, $mailName);

                if ($formData['test']) {
                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('system_administrator_mail');

                    $mail->addTo($mailAddress, 'System Administrator');
                } else {
                    foreach($recipients as $recipient)
                        $mail->addBcc($recipient->getAcademic()->getEmail(), $recipient->getAcademic()->getFullName());
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
                    'admin_mail_bakske',
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
