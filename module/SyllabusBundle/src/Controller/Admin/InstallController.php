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
	    	)
	    );
	}
	
	protected function _initAcl()
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
	                	'articles', 'manage', 'search', 'subject', 'typeahead'
	                ),
	                'admin_update_syllabus' => array(
	                	'index', 'xml'
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
}