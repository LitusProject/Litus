<?php

namespace CudiBundle\Controller\Admin;

use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * MailController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MailController extends \CudiBundle\Component\Controller\ActionController
{
    public function sendAction()
    {
        $this->initAjax();

        $form = $this->getForm('cudi_mail_send');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody($formData['message'])
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($formData['email'], $formData['name'])
                    ->setSubject($formData['subject']);

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
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
}
