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
 
namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function initConfig()
	{
	    $this->installConfig(
	        array(
	    		array(
	    			'key'         => 'syllabus.update_socket_port',
	    			'value'       => '8898',
	    			'description' => 'The port used for the websocket of the syllabus update',
	    		),
	    		array(
	    			'key'         => 'syllabus.update_socket_remote_host',
	    			'value'       => '127.0.0.1',
	    			'description' => 'The remote host for the websocket of the syllabus update',
	    		),
	    		array(
	    			'key'         => 'syllabus.update_socket_host',
	    			'value'       => '127.0.0.1',
	    			'description' => 'The host used for the websocket of the syllabus update',
	    		),
	    		array(
					'key'         => 'search_max_results',
					'value'       => '30',
					'description' => 'The maximum number of search results shown',
				),
				array(
					'key'         => 'syllabus.xml_url',
					'value'       => 'http://onderwijsaanbod.kuleuven.be/2012/opleidingen/n/xml/SC_51016934.xml',
					'description' => 'The url to the xml',
				),
	    	)
	    );
	    
	    $this->_installAcademicYear();
	}
	
	protected function initAcl()
	{
	    $this->installAcl(
	        array(
	            'syllabusbundle' => array(
	                'admin_prof' => array(
	                    'add', 'delete', 'typeahead'
	                ),
	                'admin_study' => array(
	                	'manage', 'search'
	                ),
	                'admin_subject' => array(
	                	'manage', 'search', 'subject', 'typeahead'
	                ),
	                'admin_update_syllabus' => array(
	                	'index'
	                ),
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	            'prof' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                ),
	            )
	        )
	    );
	}
	
	private function _installAcademicYear()
	{
	    $now = new DateTime('now');
	    $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            $now
        );

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($startAcademicYear);

        if (null === $academicYear) {
            $endAcademicYear = AcademicYear::getStartOfAcademicYear(
                $now->add(
                    new DateInterval('P1Y')
                )
            );
            $academicYear = new AcademicYearEntity($startAcademicYear, $endAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }
	}
}