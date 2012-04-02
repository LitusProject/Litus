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
 
namespace ProfBundle\Controller\Admin;

/**
 * InstallerController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallerController extends \CommonBundle\Component\Controller\ActionController\InstallerController
{
	protected function _initConfig()
	{
	    $this->_installConfig(
	        array(
	    		/*array(
	    			'key'         => 'syllabus.update_socket_port',
	    			'value'       => '8898',
	    			'description' => 'The port used for the websocket of the syllabus update',
	    		),*/
	    	)
	    );
	}
	
	protected function _initAcl()
	{
	    $this->installAclStructure(
	        array(
	            'profbundle' => array(
	                /*'admin_study' => array(
	                	'manage', 'search'
	                ),*/
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	            'prof' => array(
	                'parent_roles' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                )
	            )
	        )
	    );
	}
}