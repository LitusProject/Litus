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
 
namespace CommonBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function _initConfig()
	{
	    $this->_installConfig(
            array(
                array(
        			'key'         => 'search_max_results',
        			'value'       => '30',
        			'description' => 'The maximum number of search results shown',
        		),
        		array(
        		    'key'         => 'account_deactivated_mail',
        		    'value'       => 'Dear,

Your account of litus.cc is deactivated.
Click here to activate it again: http://litus/account/activate/{{ code }}',
        		    'description' => 'The email send when an account is deactivated',
        		),
        		array(
        		    'key'         => 'account_deactivated_subject',
        		    'value'       => 'Account deactivated',
        		    'description' => 'The mail subject when an account is deactivated',
        		),
        		array(
        		    'key'         => 'system_mail_address',
        		    'value'       => 'info@litus.cc',
        		    'description' => 'The system mail address',
        		),
        		array(
        		    'key'         => 'system_mail_name',
        		    'value'       => 'Litus Project',
        		    'description' => 'The system mail name',
        		),
        	)
        );
	}
	
	protected function _initAcl()
	{
	    $this->installAcl(
	    	array(
		        'commonbundle' => array(
		            'admin_auth' => array(
		            	'index', 'authenticate', 'login', 'logout'
		            ),
		            'admin_dashboard' => array(
		            	'index'
		            ),
		            'admin_role' => array(
		            	'index', 'add', 'manage', 'edit', 'delete'
		            ),
		            'admin_user' => array(
		            	'index', 'add', 'manage', 'edit', 'delete'
		            ),
		        )
		    )
	    );
	    
	    $this->installRoles(
	        array(
	            'guest' => array(
	            	'system' => true,
	                'parents' => array(
	                ),
	                'actions' => array(
	                	'admin_auth' => array(
	                		'index', 'authenticate', 'login', 'logout'
	                	),
	                ),
	            ),
	        )
	    );
	}
}