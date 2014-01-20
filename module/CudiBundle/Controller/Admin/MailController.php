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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Form\Admin\Mail\Send as MailForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

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

        $form = new MailForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($formData['email'], $formData['name'])
                    ->setSubject($formData['subject']);

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'result' => (object) array('status' => 'success'),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

                    $formErrors[$element->getAttribute('id')] = array();
                    foreach ($errors[$element->getName()] as $errorKey => $error) {
                        $formErrors[$element->getAttribute('id')][] = $element->getMessages()[$errorKey];
                    }
                }

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array("status" => "error")
            )
        );
    }
}
