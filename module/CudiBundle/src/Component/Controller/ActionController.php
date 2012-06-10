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

use CommonBundle\Component\Util\AcademicYear;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    protected function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
    		$start = AcademicYear::getStartOfAcademicYear();
    	} else {
    	    $start = AcademicYear::getDateTime($this->getParam('academicyear'));
    	}
    	$start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($start);
    	
    	if (null === $academicYear) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No academic year was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_study',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $academicYear;
    }
    
    protected function _getActiveStockPeriod()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
            
        if (null === $period) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'There is no active stock period!'
                )
            );
            
            $this->redirect()->toRoute(
            	'admin_stock_period',
            	array(
            		'action' => 'manage'
            	)
            );
            
            return;
        }
        
        $period->setEntityManager($this->getEntityManager());
        return $period;
    }
}