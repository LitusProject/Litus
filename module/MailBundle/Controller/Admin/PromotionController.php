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
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Promotion\Mail as MailForm,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
    Zend\View\Model\ViewModel;


/**
 * PromotionController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>>
 */
class PromotionController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $from = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.mail');

        $form = new MailForm($this->getEntityManager(), $from);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $promotions = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByAcademicYear($academicYear);

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail_name');

                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($from, $mailName)
                    ->addTo($from, $mailName)
                    ->setSubject($formData['subject']);

                $emailValidator = new EmailAddressValidator();
                $i = 0;
                foreach($promotions as $promotion) {
                    if (null !== $promotion->getEmailAddress() && $emailValidator->isValid($promotion->getEmailAddress())) {
                        $i++;
                        $mail->addBcc($promotion->getEmailAddress(), $promotion->getFullName());
                    }

                    if ($i == 500) {
                        $i = 0;
                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);

                        $mail->setBcc(array());
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
                    'secretary_admin_promotion',
                    array(
                        'action' => 'manage'
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
