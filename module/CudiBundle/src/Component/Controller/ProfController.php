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
 
namespace CudiBundle\Component\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Form\Auth\Login as LoginForm,
    DateInterval,
    DateTime,
	Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController
{
	/**
     * Execute the request
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function execute(MvcEvent $e)
    {
		$result = parent::execute($e);
				
		$result['authenticatedUserObject'] = $this->getAuthentication()->getPersonObject();
		$result['authenticated'] = $this->getAuthentication()->isAuthenticated();
		$result['loginForm'] = new LoginForm($this->url()->fromRoute('prof_auth', array('action' => 'login')));
		
		$result['unionUrl'] = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('union_url');
  		
        $e->setResult($result);
        return $result;
    }
    
    protected function _getAcademicYear()
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
