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

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Articles\Options\Binding,
	CudiBundle\Entity\Articles\Options\Color,
	DateTime,
	Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function _initConfig()
	{
		$this->_installConfig(
	        array(
	            array(
	            	'key'         => 'cudi.file_path',
	            	'value'       => 'data/cudi/files',
	            	'description' => 'The path to the cudi article files',
	            ),
	            array(
	            	'key'         => 'cudi.pdf_generator_path',
	            	'value'       => 'data/cudi/pdf_generator',
	            	'description' => 'The path to the PDF generator files',
	            ),
	            array(
	            	'key'         => 'fop_command',
	            	'value'       => '/usr/local/bin/fop',
	            	'description' => 'The command to call Apache FOP',
	            ),
	            array(
	            	'key'         => 'search_max_results',
	            	'value'       => '30',
	            	'description' => 'The maximum number of search results shown',
	            ),
	            array(
	            	'key'         => 'cudi.mail',
	            	'value'       => 'cudi@vtk.be',
	            	'description' => 'The mail address of cudi',
	            ),
	            array(
	            	'key'         => 'cudi.mail_name',
	            	'value'       => 'VTK Cursusdienst',
	            	'description' => 'The name of the mail sender',
	            ),
	            array(
	            	'key'         => 'union_short_name',
	            	'value'       => 'VTK',
	            	'description' => 'The short name of this union',
	            ),
	            array(
	            	'key'         => 'union_name',
	            	'value'       => 'VTK vzw',
	            	'description' => 'The full name of this union',
	            ),
	            array(
	            	'key'         => 'union_logo',
	            	'value'       => 'data/images/logo/logo.jpg',
	            	'description' => 'The path to the logo of the union',
	            ),
	            array(
	            	'key'         => 'cudi.name',
	            	'value'       => 'Cudi',
	            	'description' => 'The name of the cudi',
	            ),
	            array(
	            	'key'         => 'cudi.person',
	            	'value'       => '1',
	            	'description' => 'The ID of the person responsible for the cudi',
	            ),
	            array(
	            	'key'         => 'cudi.delivery_address_name',
	            	'value'       => 'VTK Cursusdienst',
	            	'description' => 'The name of the delivery address of the cudi',
	            ),
	            array(
	            	'key'         => 'cudi.delivery_address_extra',
	            	'value'       => '(inrit via Celestijnenlaan)',
	            	'description' => 'The extra information of the delivery address of the cudi',
	            ),
	            array(
	            	'key'         => 'cudi.billing_address_name',
	            	'value'       => 'VTK vzw',
	            	'description' => 'The name of the billing organisation of the cudi',
	            ),
	            array(
	            	'key'         => 'cudi.reservation_expire_time',
	            	'value'       => 'P2W',
	            	'description' => 'The time after which a reservation expires',
	            ),
	            array(
    				'key'         => 'cudi.booking_assigned_mail_subject',
    				'value'       => 'New Assignments',
    				'description' => 'The subject of the mail sent by new assignments',
    			),
    			array(
    				'key'         => 'cudi.booking_assigned_mail',
    				'value'       => 'Dear,
			
The following bookings are assigned to you:
{{ bookings }}',
    				'description' => 'The mail sent when a booking is assigned'
    			),
			)
		);
		
		$this->_installAddresses();
		$this->_installBinding();
		$this->_installAcademicYear();
		$this->_installColor();
	}
	
	protected function _initAcl()
	{
	    $this->installRoles(
	        array(
	            'supplier' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                )
	            ),
	        )
	    );
	}
	
	private function _installBinding()
	{
		$bindings = array('Binded');
		
		foreach($bindings as $item) {
			$binding = $this->getEntityManager()
				->getRepository('CudiBundle\Entity\Articles\Options\Binding')
				->findOneByName($item);
			if (null == $binding) {
				$binding = new Binding($item);
				$this->getEntityManager()->persist($binding);
			}
		}
		$this->getEntityManager()->flush();
	}
	
	private function _installColor()
	{
		$colors = array('Red', 'Yellow');
		
		foreach($colors as $item) {
			$color = $this->getEntityManager()
				->getRepository('CudiBundle\Entity\Articles\Options\Color')
				->findOneByName($item);
			if (null == $color) {
				$color = new Color($item);
				$this->getEntityManager()->persist($color);
			}
		}
		$this->getEntityManager()->flush();
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
	
	private function _installAddresses()
	{
	    try {
	    	$config = $this->getEntityManager()
	    		->getRepository('CommonBundle\Entity\General\Config')
	    		->getConfigValue('cudi.delivery_address');
	    } catch(Exception $e) {
	        $address = new Address(
	            'Kasteelpark Arenberg',
	            41,
	            3001,
	            'Heverlee',
	            'BE'
	        );
	        $this->getEntityManager()->persist($address);
	    	$config = new Config('cudi.delivery_address', (string) $address->getId());
	    	$config->setDescription('The delivery address of the cudi');
	    	$this->getEntityManager()->persist($config);
	    }
	    
	    try {
	    	$config = $this->getEntityManager()
	    		->getRepository('CommonBundle\Entity\General\Config')
	    		->getConfigValue('cudi.billing_address');
	    } catch(Exception $e) {
	        $address = new Address(
	            'Studentenwijk Arenberg',
	            '6/0',
	            3001,
	            'Heverlee',
	            'BE'
	        );
	        $this->getEntityManager()->persist($address);
	    	$config = new Config('cudi.billing_address', (string) $address->getId());
	    	$config->setDescription('The billing address of the cudi');
	    	$this->getEntityManager()->persist($config);
	    }
	}
}