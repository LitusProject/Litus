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
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime,
    MailBundle\Form\Admin\Cudi\Mail as MailForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ProfController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */    
class ProfController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function cudiAction()
    {
        $academicYear = $this->_getAcademicYear();
    
        $semester = (new DateTime() < $academicYear->getUniversityStartDate()) ? 1 : 2;
        
        $mailSubject = str_replace(
            array(
                '{{ semester }}',
                '{{ academicYear }}',
            ),
            array(
                $semester,
                $academicYear->getCode(),
            ),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('mail.start_cudi_mail_subject')
        );
        
        $mail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.start_cudi_mail');
        
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
                    
                $statuses = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\Statuses\University')
                    ->findAllByStatus('professor', $academicYear);
                
                foreach($statuses as $status) {
                    $allSubjects = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                        ->findAllByProfAndAcademicYear($status->getPerson(), $academicYear);
                        
                    $subjects = array();
                    foreach($allSubjects as $subject) {
                        if ($subject->getSubject()->getSemester() == $semester || $subject->getSubject()->getSemester() == 3) {
                            $subjects[] = $subject->getSubject();
                        }
                    }
                    
                    if (sizeof($subjects) == 0)
                        continue;
                        
                    $text = '';
                    foreach($subjects as $subject)
                        $text .= ' - ' . $subject->getName() . PHP_EOL;
        
                    $body = str_replace('{{ subjects }}', ' ' . trim($text), $mail);

                    $message = new Message();
                    $message->setBody($body)
                        ->setFrom($mailAddress, $mailName)
                        ->setSubject($mailSubject);
                        
                    $message->addBcc($mailAddress);
                    
                    if ($formData['test_it']) {
                        $message->addTo(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_administrator_mail'),
                            'System Administrator'
                        );
                    } else {
                        $message->addTo($status->getPerson()->getEmail(), $status->getPerson()->getFullName());                    
                    }
                       
                    if ('production' == getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($message);
                    
                    if ($formData['test_it'])
                        break;
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The mail was successfully sent!'
                    )
                );
                
                $this->redirect()->toRoute(
                    'admin_mail_prof',
                    array(
                        'action' => 'cudi'
                    )
                );
                
                return new ViewModel();
            }
        }
        
        return new ViewModel(
            array(
                'subject' => $mailSubject,
                'mail' => $mail,
                'semester' => $semester,
                'form' => $form,
            )
        );
    }
     
    private function _getAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);
                
        $now = new DateTime();
        $profStart = new DateTime($this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.prof_start_academic_year'));
        if ($now > $profStart) {
            $startAcademicYear->add(new DateInterval('P1Y2M'));
            $startAcademicYear = AcademicYear::getStartOfAcademicYear($startAcademicYear);
        }

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);
        
        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        return $academicYear;
    }
}
