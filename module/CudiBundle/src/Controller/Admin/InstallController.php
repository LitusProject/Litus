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
    CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Articles\Options\Binding,
	CudiBundle\Entity\Articles\Options\Color,
	DateInterval,
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
	            	'key'         => 'union_url',
	            	'value'       => 'http://www.vtk.be',
	            	'description' => 'The URL of the union',
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
{{ bookings }}

These reservations will expire after the first sale session after it\'s expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

VTK Cudi

-- This is an automatically generated email, please do not reply --',
    				'description' => 'The mail sent when a booking is assigned'
    			),
    			array(
    				'key'         => 'cudi.queue_item_barcode_prefix',
    				'value'       => '988000000000',
    				'description' => 'The start for a serving queue item barcode',
    			),
    			array(
    				'key'         => 'cudi.queue_socket_port',
    				'value'       => '8899',
    				'description' => 'The port used for the websocket of the queue',
    			),
    			array(
    				'key'         => 'cudi.queue_socket_remote_host',
    				'value'       => '127.0.0.1',
    				'description' => 'The remote host for the websocket of the queue',
    			),
    			array(
    				'key'         => 'cudi.queue_socket_host',
    				'value'       => '127.0.0.1',
    				'description' => 'The host used for the websocket of the queue',
    			),
    			array(
    			    'key'         => 'cudi.prof_start_academic_year',
    			    'value'       => '2012-8-1 0:0:0',
    			    'description' => 'The start date of the academic year for a prof',
    			),
			)
		);
		
		$this->_installAddresses();
		$this->_installBinding();
		$this->_installAcademicYear();
		$this->_installColor();
		$this->_installMoneyUnit();
		$this->_installBankDevice();
	}
	
	protected function _initAcl()
	{
	    $this->installAcl(
	        array(
	            'cudibundle' => array(
	                'admin_article' => array(
	                    'add', 'delete', 'edit', 'manage', 'search'
	                ),
	                'admin_article_comment' => array(
	                    'delete', 'manage'
	                ),
	                'admin_article_file' => array(
	                    'delete', 'download', 'edit', 'manage', 'progress', 'upload'
	                ),
	                'admin_article_subject' => array(
	                    'delete', 'manage'
	                ),
	                'admin_prof_action' => array(
	                	'completed', 'confirmArticle', 'confirmFile', 'manage', 'refused', 'view'
	                ),
	                'admin_sales_article' => array(
	                    'activate', 'add', 'delete', 'edit', 'manage', 'search', 'sellProf'
	                ),
	                'admin_sales_booking' => array(
	                    'add', 'assign', 'delete', 'inactive', 'manage', 'search', 'unassign'
	                ),
	                'admin_sales_discount' => array(
	                    'delete', 'manage'
	                ),
	                'admin_sales_financial' => array(
	                    'deliveries', 'retours', 'sales', 'stock', 'supplier'
	                ),
	                'admin_sales_session' => array(
	                    'add', 'close', 'edit', 'editRegister', 'manage', 'queueItems'
	                ),
	                'admin_stock' => array(
	                    'delta', 'edit', 'manage', 'search'
	                ),
	                'admin_stock_delivery' => array(
	                    'add', 'delete', 'manage', 'supplier'
	                ),
	                'admin_stock_order' => array(
	                    'add', 'delete', 'edit', 'export', 'manage', 'pdf', 'supplier'
	                ),
	                'admin_stock_period' => array(
	                    'manage', 'new', 'search', 'view'
	                ),
	                'admin_stock_retour' => array(
	                    'add', 'delete', 'manage', 'supplier'
	                ),
	                'admin_supplier' => array(
	                    'add', 'edit', 'manage'
	                ),
	                'admin_supplier_user' => array(
	                    'add', 'delete', 'edit', 'manage'
	                ),
	                'prof_article' => array(
	                    'add', 'edit', 'manage', 'typeahead'
	                ),
	                'prof_article_mapping' => array(
	                    'add', 'delete'
	                ),
	                'prof_comment' => array(
	                    'delete', 'manage'
	                ),
	                'prof_file' => array(
	                    'delete', 'download', 'manage', 'progress', 'upload'
	                ),
	                'prof_index' => array(
	                    'index'
	                ),
	                'prof_prof' => array(
	                    'add', 'delete', 'typeahead'
	                ),
	                'prof_subject' => array(
	                    'manage', 'subject', 'typeahead'
	                ),
	                'sale_queue' => array(
	                    'overview', 'signin'
	                ),
	                'sale_sale' => array(
	                    'return', 'sale', 'saveComment'
	                ),
	                'supplier_article' => array(
	                    'manage'
	                ),
	                'supplier_index' => array(
	                    'index'
	                ),
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	            'supplier' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                    'supplier_article' => array(
	                        'manage'
	                    ),
	                    'supplier_index' => array(
	                        'index'
	                    ),
	                )
	            ),
	            'prof' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                    'prof_article' => array(
	                        'add', 'edit', 'manage', 'typeahead'
	                    ),
	                    'prof_article_mapping' => array(
	                        'add', 'delete'
	                    ),
	                    'prof_comment' => array(
	                        'delete', 'manage'
	                    ),
	                    'prof_file' => array(
	                        'delete', 'download', 'manage', 'progress', 'upload'
	                    ),
	                    'prof_index' => array(
	                        'index'
	                    ),
	                    'prof_prof' => array(
	                        'add', 'delete', 'typeahead'
	                    ),
	                    'prof_subject' => array(
	                        'manage', 'subject', 'typeahead'
	                    ),
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
	
	private function _installMoneyUnit()
	{
		$units = array(500, 200, 100, 50, 20, 10, 5, 2, 1, 0.50, 0.20, 0.10, 0.05, 0.02, 0.01);
		
		foreach($units as $item) {
			$unit = $this->getEntityManager()
				->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
				->findOneByUnit($item);
			if (null == $unit) {
				$unit = new MoneyUnit($item);
				$this->getEntityManager()->persist($unit);
			}
		}
		$this->getEntityManager()->flush();
	}
	
	private function _installBankDevice()
	{
		$bankdevices = array('Device 1', 'Device 2');
		
		foreach($bankdevices as $item) {
			$bankdevice = $this->getEntityManager()
				->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
				->findOneByName($item);
			if (null == $bankdevice) {
				$bankdevice = new BankDevice($item);
				$this->getEntityManager()->persist($bankdevice);
			}
		}
		$this->getEntityManager()->flush();
	}
}