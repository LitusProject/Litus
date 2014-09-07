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

use Zend\Mail\Message,
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
        $form = $this->getForm('mail_promotion_mail');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $people = $this->_getPeople($formData['to']);

                $from = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail_name');

                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($from, $mailName)
                    ->addTo($from, $mailName)
                    ->setSubject($formData['subject']);

                $i = 0;
                foreach ($people as $person) {
                    if (null !== $person->getEmailAddress()) {
                        $i++;
                        $mail->addBcc($person->getEmailAddress(), $person->getFullName());
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

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_promotion',
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

    private function _getPeople($listTo)
    {
        $people = array();
        foreach ($listTo as $to) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneById($to);

            $people = array_merge(
                $people,
                $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByAcademicYear($academicYear)
            );
        }

        return $people;
    }
}
