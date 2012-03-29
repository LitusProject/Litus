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
 * ConfigController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallerController extends \CommonBundle\Component\Controller\ActionController\InstallerController
{
	protected function _initConfig()
	{
	}
	
	protected function _initAcl()
	{
	    $this->installAclStructure(
	        array(
	            'syllabusbundle' => array(
	                'admin_study' => array(
	                	'manage', 'search'
	                ),
	                'admin_subject' => array(
	                	'manage', 'search'
	                ),
	                'admin_update_syllabus' => array(
	                	'update'
	                ),
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	        )
	    );
	}
}