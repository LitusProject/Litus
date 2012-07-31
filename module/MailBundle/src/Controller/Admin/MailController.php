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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    MailBundle\Form\Admin\Mail\Mail as MailForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * MailController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */    
class MailController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function groupsAction()
    {
        return new ViewModel(
            array(
                'university' => UniversityStatus::$possibleStatuses,
                'organization' => OrganizationStatus::$possibleStatuses,
            )
        );
    }
    
    public function sendAction()
    {
        if (!($type = $this->_getType()))
            return new ViewModel();
        
        if ('organization' == $type) {
            if (!($status = $this->_getOrganizationStatus()))
                return new ViewModel();
            $statuses = OrganizationStatus::$possibleStatuses;
        } else {
            if (!($status = $this->_getUniversityStatus()))
                return new ViewModel();
            $statuses = UniversityStatus::$possibleStatuses;
        }
        
        $form = new MailForm();
        
        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_mail_address');
                    
                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_mail_name');
                    
                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($mailAddress, $mailName)
                    ->setSubject($formData['subject']);
                    
                if ('organization' == $type) {
                    $persons = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\Statuses\Organization')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear());
                } else {
                    $persons = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\Statuses\University')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear());
                }
                
                foreach($persons as $person)
                    $mail->addTo($person->getPerson()->getEmail(), $person->getPerson()->getFullName());
                
                if ('production' == getenv('APPLICATION_ENV'))
                    $mailTransport->send($mail);
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The mail was successfully send!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_mail',
                    array(
                        'action' => 'groups'
                    )
                );
                
                return new ViewModel();
            }
        }
        
        return new ViewModel(
            array(
                'type' => $type,
                'status' => $statuses[$status],
                'form' => $form,
            )
        );
    }
    
    private function _getType()
    {
        if (null === $this->getParam('type')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No university status given to send a mail to!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        };
        
        $type = $this->getParam('type');
        
        if ('organization' != $type && 'university' != $type) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No university status given to send a mail to!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        }
        
        return $type;
    }
    
    private function _getUniversityStatus()
    {
        if (null === $this->getParam('group')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No university status given to send a mail to!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        };
        
        $status = $this->getParam('group');
        
        if (!array_key_exists($status, UniversityStatus::$possibleStatuses)) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given university status was not valid!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        }
        
        return $status;
    }    
    
    private function _getOrganizationStatus()
    {
        if (null === $this->getParam('group')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No organization status given to send a mail to!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        };
        
        $status = $this->getParam('group');
        
        if (!array_key_exists($status, OrganizationStatus::$possibleStatuses)) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given organization status was not valid!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_mail',
                array(
                    'action' => 'groups'
                )
            );
            
            return;
        }
        
        return $status;
    }
}
