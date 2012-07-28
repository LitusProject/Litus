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
	    $subject = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('mail.start_cudi_mail_subject');
	    
	    $mail = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('mail.start_cudi_mail');
	    	
	    $academicYear = $this->_getAcademicYear();
	    
        $semester = (new DateTime() < $academicYear->getStartDate()) ? 1 : 2;
	    
	    return new ViewModel(
	        array(
	            'subject' => $subject,
	            'mail' => $mail,
	            'semester' => $semester,
	        )
	    );
	}
	
	public function sendAction()
	{
	    $academicYear = $this->_getAcademicYear();
	                
	    $semester = (new DateTime() < $academicYear->getStartDate()) ? 1 : 2;
	    
	    $statuses = $this->getEntityManager()
	        ->getRepository('CommonBundle\Entity\Users\Statuses\University')
	        ->findAllByStatus('professor', $academicYear);
	        
	    $mailSubject = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('mail.start_cudi_mail_subject');
	    
	    $mail = $this->getEntityManager()
	    	->getRepository('CommonBundle\Entity\General\Config')
	    	->getConfigValue('mail.start_cudi_mail');
	    	
    	$mailAddress = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('cudi.mail');
    		
    	$mailName = $this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('cudi.mail_name');
	    
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
	        	->addTo($status->getPerson()->getEmail(), $status->getPerson()->getFullName())
	        	->setSubject($mailSubject);
	           
	        if ('production' == getenv('APPLICATION_ENV'))
	            $mailTransport->send($message);
	    }

	    $this->flashMessenger()->addMessage(
	        new FlashMessage(
	            FlashMessage::SUCCESS,
	            'Success',
	            'The mail was successfully send!'
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
	
	private function _getAcademicYear()
    {
   		$start = AcademicYear::getStartOfAcademicYear();
    	$start->setTime(0, 0);
    	    	
    	$now = new DateTime();
    	$profStart = new DateTime($this->getEntityManager()
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('cudi.prof_start_academic_year'));
    	if ($now > $profStart) {
    	    $start->add(new DateInterval('P1Y2M'));
    	    $start = AcademicYear::getStartOfAcademicYear($start);
    	}

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($start);
    	
    	if (null === $academicYear) {
    	    $endAcademicYear = AcademicYear::getStartOfAcademicYear(
    	        $now->add(
    	            new DateInterval('P1Y')
    	        )
    	    );
    	    $academicYear = new AcademicYearEntity($start, $endAcademicYear);
    	    $this->getEntityManager()->persist($academicYear);
    	    $this->getEntityManager()->flush();
    	}

    	return $academicYear;
    }
}