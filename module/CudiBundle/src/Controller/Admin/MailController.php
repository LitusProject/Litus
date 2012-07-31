<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
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
                
                if ('production' == getenv('APPLICATION_ENV'))
                    $mailTransport->send($mail);
               
                
                return new ViewModel(
                    array(
                        'status' => 'success',
                        'result' => (object) array("status" => "success"),
                    )
                );
            } else {
                $errors = $form->getErrors();
                $formErrors = array();
                
                foreach ($form->getElements() as $key => $element) {
                    $formErrors[$element->getId()] = array();
                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getId()][] = $element->getMessages()[$error];
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
